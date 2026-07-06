<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Models\Room;
use App\Models\Cases;
use App\Models\Contact;
use App\Models\Feature;
use App\Models\BookingParticipant;
use App\Models\BookingFeature;
use App\Models\BookingBreakoutRoom;
use Carbon\Carbon;

class ImportBookingsCommand extends Command
{
    protected $signature = 'import:bookings {file : Path to CSV file}';
    protected $description = 'Import bookings from Excel CSV export';

    private $skippedLog = [];

    public function handle()
    {
        $file = $this->argument('file');
        if (!file_exists($file)) { $this->error("File not found: {$file}"); return 1; }

        // Use fgetcsv to handle multi-line quoted fields
        $csv = [];
        if (($handle = fopen($file, 'r')) !== false) {
            while (($row = fgetcsv($handle)) !== false) {
                $csv[] = $row;
            }
            fclose($handle);
        }
        $headers = array_shift($csv);

        $this->info("Found " . count($csv) . " rows to import.");
        $bar = $this->output->createProgressBar(count($csv));
        $bar->start();

        $imported = 0; $skipped = 0;

        foreach ($csv as $i => $row) {
            $rowNumber = $i + 2;
            $row = array_pad($row, count($headers), '');
            if (count($row) < 5) { $this->logSkip($rowNumber, $row, 'Too few fields'); $skipped++; $bar->advance(); continue; }

            try { $data = array_combine($headers, $row); }
            catch (\ValueError $e) { $this->logSkip($rowNumber, $row, 'Field count mismatch'); $skipped++; $bar->advance(); continue; }

            if (empty($data['Booking ID'])) { $this->logSkip($rowNumber, $data, 'Empty Booking ID'); $skipped++; $bar->advance(); continue; }

            $date = $this->parseDate($data['Hearing Date']);
            if (!$date) { $this->logSkip($rowNumber, $data, 'Invalid date: ' . ($data['Hearing Date'] ?? 'empty')); $skipped++; $bar->advance(); continue; }

            $room = Room::where('room_name', $data['Room'])->first();
            if (!$room) { $this->logSkip($rowNumber, $data, 'Room not found: ' . ($data['Room'] ?? 'empty')); $skipped++; $bar->advance(); continue; }

            if (Booking::where('booking_id', $data['Booking ID'])->where('booking_date', $date)->exists()) {
                $this->logSkip($rowNumber, $data, 'Duplicate Booking ID + Date: ' . $data['Booking ID']); $skipped++; $bar->advance(); continue;
            }

            $sessionType = $this->parseSession($data['Session'] ?? '');
            $times = $this->getTimes($sessionType);
            $case = null;
            if (!empty($data['Reference Number'])) {
                $case = Cases::firstOrCreate(['reference_number' => $data['Reference Number']], ['status' => 'active']);
            }

            $booking = Booking::create([
                'booking_id' => $data['Booking ID'], 'case_id' => $case?->id, 'room_id' => $room->id,
                'booking_date' => $date, 'session_type' => $sessionType, 'start_time' => $times[0], 'end_time' => $times[1],
                'booking_type' => 'external', 'booking_status' => 'confirmed', 'billing_status' => 'pending', 'booked_by' => 1,
            ]);

            $order = 0;
            foreach (['Claimant', 'Claimant Solicitor', 'Respondent', 'Respondent Solicitor'] as $field) {
                if (!empty($data[$field])) {
                    $cleaned = $this->extractBreakoutRooms($data[$field]);
                    foreach ($cleaned['breakout_rooms'] as $brCode) {
                        $br = Room::where('room_code', $brCode)->first();
                        if ($br) BookingBreakoutRoom::firstOrCreate(['booking_id' => $booking->id, 'room_id' => $br->id]);
                    }
                    if (!empty($cleaned['name'])) {
                        $contact = $this->findOrCreateContact($cleaned['name']);
                        BookingParticipant::create(['booking_id' => $booking->id, 'contact_id' => $contact->id, 'role' => strtolower(str_replace(' ', '_', $field)), 'display_order' => $order++]);
                    }
                }
            }

            if (!empty($data['Arbitrators'])) {
                foreach (explode(',', $data['Arbitrators']) as $i => $name) {
                    $name = trim($name);
                    if ($name) {
                        $contact = $this->findOrCreateContact($name);
                        BookingParticipant::create(['booking_id' => $booking->id, 'contact_id' => $contact->id, 'role' => $i === 0 ? 'presiding_arbitrator' : 'co_arbitrator', 'display_order' => $order++]);
                    }
                }
            }

            if (!empty($data['Features'])) {
                foreach ($this->parseFeatures($data['Features']) as $fName) {
                    $feature = Feature::where('name', $fName)->first();
                    if ($feature) BookingFeature::firstOrCreate(['booking_id' => $booking->id, 'feature_id' => $feature->id]);
                }
            }

            foreach (['1st Breakout Room', '2nd Breakout Room', '3rd Breakout Room'] as $col) {
                if (!empty($data[$col])) {
                    $br = Room::where('room_name', $data[$col])->first();
                    if ($br) BookingBreakoutRoom::firstOrCreate(['booking_id' => $booking->id, 'room_id' => $br->id]);
                }
            }

            $imported++; $bar->advance();
        }

        $bar->finish();
        $this->info("\nDone. Imported: {$imported}, Skipped: {$skipped}");

        if (!empty($this->skippedLog)) {
            $logFile = storage_path('logs/import_skipped_' . now()->format('Ymd_His') . '.log');
            if (!is_dir(dirname($logFile))) mkdir(dirname($logFile), 0755, true);
            file_put_contents($logFile, implode("\n", $this->skippedLog));
            $this->info('Skipped log: ' . $logFile);
        }
        return 0;
    }

    private function logSkip($rowNumber, $data, $reason): void
    {
        $bid = is_array($data) ? ($data['Booking ID'] ?? 'N/A') : 'N/A';
        $this->skippedLog[] = "Row {$rowNumber} | Booking ID: {$bid} | Reason: {$reason}";
    }

    private function extractBreakoutRooms($value): array
    {
        $brs = []; $clean = $value;
        if (preg_match_all('/\[\+BR\s*([^\]]+)\]/i', $value, $m)) {
            foreach ($m[1] as $x) foreach (preg_split('/[, ]+/', trim($x)) as $n) if (is_numeric($n)) $brs[] = 'BR'.intval($n);
            $clean = trim(preg_replace('/\[\+BR[^\]]*\]/i', '', $value));
        }
        return ['name' => $clean, 'breakout_rooms' => array_unique($brs)];
    }

    private function parseDate($value): ?string
    { if (empty($value)) return null; try { return Carbon::parse($value)->toDateString(); } catch (\Exception $e) { return null; } }

    private function parseSession($value): string
    { return match(trim($value)) { 'Full Day' => 'full_day', 'Halfday (AM)', 'Half Day AM' => 'half_am', 'Halfday (PM)', 'Half Day PM' => 'half_pm', 'Overtime' => 'overtime', default => 'full_day' }; }

    private function getTimes($s): array
    { return match($s) { 'full_day' => ['09:00:00','17:00:00'], 'half_am' => ['09:00:00','13:00:00'], 'half_pm' => ['14:00:00','17:00:00'], default => ['09:00:00','17:00:00'] }; }

    private function parseFeatures($value): array
    { $f=[]; foreach(explode('|',$value) as $p) { $p=trim($p); if(in_array($p,['CRT','Recording (ZOOM)'])) $f[]='Recording'; elseif($p==='VC') $f[]='VC'; elseif($p==='Projector') $f[]='Projector'; } return array_unique($f); }

    private function findOrCreateContact($name): Contact
    { return Contact::firstOrCreate(['name' => trim($name)], ['type' => 'individual']); }
}
