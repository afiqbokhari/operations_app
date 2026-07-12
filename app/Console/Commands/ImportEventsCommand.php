<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Event;
use App\Models\Room;
use App\Models\Feature;
use Carbon\Carbon;

class ImportEventsCommand extends Command
{
    protected $signature = 'import:events {file : Path to CSV file}';
    protected $description = 'Import events/meetings from Excel CSV export';

    private $skippedLog = [];

    public function handle()
    {
        $file = $this->argument('file');
        if (!file_exists($file)) { $this->error("File not found: {$file}"); return 1; }

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

            $date = $this->parseDate($data['Hearing Date']);
            if (!$date) { $this->logSkip($rowNumber, $data, 'Invalid date: ' . ($data['Hearing Date'] ?? 'empty')); $skipped++; $bar->advance(); continue; }

            $room = Room::where('room_name', $data['Room'])->first();
            if (!$room) { $this->logSkip($rowNumber, $data, 'Room not found: ' . ($data['Room'] ?? 'empty')); $skipped++; $bar->advance(); continue; }

            // Event name: Matter Between > Reference Number > Booking ID > "Untitled Event"
            $eventName = $data['Matter Between'] ?? '';
            if (empty(trim($eventName))) $eventName = $data['Reference Number'] ?? '';
            if (empty(trim($eventName))) $eventName = $data['Booking ID'] ?? '';
            if (empty(trim($eventName))) $eventName = 'Untitled Event';

            $sessionType = $this->parseSession($data['Session'] ?? '');
            $times = $this->getTimes($sessionType);

            // Duplicate check: same event name + date + room
            if (Event::where('event_name', $eventName)->where('start_date', $date)->where('room_id', $room->id)->exists()) {
                $this->logSkip($rowNumber, $data, 'Duplicate: ' . $eventName . ' on ' . $date);
                $skipped++; $bar->advance(); continue;
            }

            $event = Event::create([
                'event_name' => $eventName,
                'event_type' => 'meeting',
                'reference_number' => $data['Reference Number'] ?? null,
                'room_id' => $room->id,
                'start_date' => $date,
                'end_date' => $date,
                'start_time' => $times[0],
                'end_time' => $times[1],
                'organizer' => null,
                'status' => 'approved',
                'booked_by' => 1,
                'notes' => $data['Booking ID'] ?? null,
            ]);

            // Features
            if (!empty($data['Features'])) {
                foreach ($this->parseFeatures($data['Features']) as $fName) {
                    $feature = Feature::where('name', $fName)->first();
                    if ($feature) {
                        \DB::table('event_features')->insertOrIgnore([
                            'event_id' => $event->id,
                            'feature_id' => $feature->id,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }

            // Breakout rooms from columns
            foreach (['1st Breakout Room', '2nd Breakout Room', '3rd Breakout Room'] as $col) {
                if (!empty($data[$col])) {
                    $br = Room::where('room_name', $data[$col])->first();
                    if ($br) {
                        \DB::table('event_breakout_rooms')->insertOrIgnore([
                            'event_id' => $event->id,
                            'room_id' => $br->id,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }

            // Breakout rooms from [+BR] notation in Matter Between / Reference Number
            foreach (['Matter Between', 'Reference Number'] as $field) {
                if (!empty($data[$field])) {
                    $extracted = $this->extractBreakoutRooms($data[$field]);
                    foreach ($extracted as $brCode) {
                        $br = Room::where('room_code', $brCode)->first();
                        if ($br) {
                            \DB::table('event_breakout_rooms')->insertOrIgnore([
                                'event_id' => $event->id,
                                'room_id' => $br->id,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }
                }
            }

            $imported++; $bar->advance();
        }

        $bar->finish();
        $this->info("\nDone. Imported: {$imported}, Skipped: {$skipped}");

        if (!empty($this->skippedLog)) {
            $logFile = storage_path('logs/import_events_skipped_' . now()->format('Ymd_His') . '.log');
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
        $brs = [];
        if (preg_match_all('/\[\+\s*BR\s*([^\]]+)\]/i', $value, $m)) {
            foreach ($m[1] as $x) foreach (preg_split('/[, ]+/', trim($x)) as $n) if (is_numeric($n)) $brs[] = 'BR'.intval($n);
        }
        return array_unique($brs);
    }

    private function parseDate($value): ?string
    { if (empty($value)) return null; try { return Carbon::parse($value)->toDateString(); } catch (\Exception $e) { return null; } }

    private function parseSession($value): string
    { return match(trim($value)) { 'Full Day' => 'full_day', 'Halfday (AM)', 'Half Day AM' => 'half_am', 'Halfday (PM)', 'Half Day PM' => 'half_pm', 'Overtime' => 'overtime', default => 'full_day' }; }

    private function getTimes($s): array
    { return match($s) { 'full_day' => ['09:00:00','17:00:00'], 'half_am' => ['09:00:00','13:00:00'], 'half_pm' => ['14:00:00','17:00:00'], default => ['09:00:00','17:00:00'] }; }

    private function parseFeatures($value): array
    { $f=[]; foreach(explode('|',$value) as $p) { $p=trim($p); if(in_array($p,['CRT','Recording (ZOOM)'])) $f[]='Recording'; elseif($p==='VC') $f[]='VC'; elseif($p==='Projector') $f[]='Projector'; } return array_unique($f); }
}
