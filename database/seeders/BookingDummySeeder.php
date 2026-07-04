<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Booking;
use App\Models\Cases;
use App\Models\Contact;
use App\Models\Room;
use App\Models\Feature;
use App\Models\BookingParticipant;
use App\Models\BookingFeature;
use Carbon\Carbon;

class BookingDummySeeder extends Seeder
{
    public function run(): void
    {
        // Create dummy contacts if none exist
        if (Contact::count() < 10) {
            $contacts = [
                ['name' => 'ABC Holdings Sdn Bhd', 'type' => 'company'],
                ['name' => 'XYZ Corporation', 'type' => 'company'],
                ['name' => 'Tan Sri Ahmad Razak', 'type' => 'individual', 'designation' => 'Tan Sri'],
                ['name' => 'Datuk Lim Wei Ming', 'type' => 'individual', 'designation' => 'Datuk'],
                ['name' => 'Dato\' Maria Yusof', 'type' => 'individual', 'designation' => 'Dato\''],
                ['name' => 'Mr David Chen', 'type' => 'individual', 'designation' => 'Mr'],
                ['name' => 'M/s Raj & Associates', 'type' => 'company'],
                ['name' => 'M/s Tan & Partners', 'type' => 'company'],
                ['name' => 'Ms Sarah Lee', 'type' => 'individual', 'designation' => 'Ms'],
                ['name' => 'Mr Kumar Devan', 'type' => 'individual', 'designation' => 'Mr'],
                ['name' => 'Mega Construction Sdn Bhd', 'type' => 'company'],
                ['name' => 'Tech Solutions Bhd', 'type' => 'company'],
                ['name' => 'M/s Wong & Co', 'type' => 'company'],
                ['name' => 'M/s Lim Advocates', 'type' => 'company'],
            ];

            foreach ($contacts as $contact) {
                Contact::create($contact);
            }
        }

        $contacts = Contact::all()->keyBy('name');
        $rooms = Room::where('type', 'hearing_room')->where('status', 'active')->inRandomOrder()->get();
        $features = Feature::all()->keyBy('name');

        $dummyBookings = [
            [
                'booking_id' => 'AIAC-2026-001',
                'case_ref' => 'ARB-2026-001',
                'claimant' => 'ABC Holdings Sdn Bhd',
                'claimant_solicitor' => 'M/s Raj & Associates',
                'respondent' => 'XYZ Corporation',
                'respondent_solicitor' => 'M/s Tan & Partners',
                'arbitrators' => ['Tan Sri Ahmad Razak', 'Datuk Lim Wei Ming', 'Dato\' Maria Yusof'],
                'features' => ['VC', 'Recording'],
                'session' => 'full_day',
            ],
            [
                'booking_id' => 'AIAC-2026-002',
                'case_ref' => 'ARB-2026-002',
                'claimant' => 'Mega Construction Sdn Bhd',
                'claimant_solicitor' => 'M/s Wong & Co',
                'respondent' => 'Tech Solutions Bhd',
                'respondent_solicitor' => 'M/s Lim Advocates',
                'arbitrators' => ['Mr David Chen'],
                'features' => ['Projector', 'Smart Screen'],
                'session' => 'half_am',
            ],
            [
                'booking_id' => 'AIAC-2026-003',
                'case_ref' => 'ARB-2026-003',
                'claimant' => 'XYZ Corporation',
                'claimant_solicitor' => 'M/s Tan & Partners',
                'respondent' => 'ABC Holdings Sdn Bhd',
                'respondent_solicitor' => 'M/s Raj & Associates',
                'arbitrators' => ['Ms Sarah Lee', 'Mr Kumar Devan'],
                'features' => ['VC'],
                'session' => 'half_pm',
            ],
            [
                'booking_id' => 'AIAC-2026-004',
                'case_ref' => 'ARB-2026-004',
                'claimant' => 'Tech Solutions Bhd',
                'claimant_solicitor' => 'M/s Lim Advocates',
                'respondent' => 'Mega Construction Sdn Bhd',
                'respondent_solicitor' => 'M/s Wong & Co',
                'arbitrators' => ['Tan Sri Ahmad Razak'],
                'features' => ['Recording', 'Projector'],
                'session' => 'full_day',
            ],
            [
                'booking_id' => 'AIAC-2026-005',
                'case_ref' => 'ARB-2026-005',
                'claimant' => 'ABC Holdings Sdn Bhd',
                'claimant_solicitor' => 'M/s Raj & Associates',
                'respondent' => 'Mega Construction Sdn Bhd',
                'respondent_solicitor' => 'M/s Wong & Co',
                'arbitrators' => ['Datuk Lim Wei Ming', 'Dato\' Maria Yusof', 'Mr David Chen'],
                'features' => ['VC', 'Recording', 'Smart Screen'],
                'session' => 'half_am',
            ],
        ];

        $today = Carbon::today();
        $roomIndex = 0;
        $dayOffset = 0;

        foreach ($dummyBookings as $data) {
            $bookingDate = $today->copy()->addDays($dayOffset);
            // Skip weekends
            while ($bookingDate->isWeekend()) {
                $dayOffset++;
                $bookingDate = $today->copy()->addDays($dayOffset);
            }

            $room = $rooms[$roomIndex % count($rooms)];
            $roomIndex++;

            // Alternate between AM and PM for same-day bookings
            if ($roomIndex % 2 == 0 && $roomIndex > 0) {
                $dayOffset++;
            }

            $case = Cases::firstOrCreate(
                ['reference_number' => $data['case_ref']],
                ['status' => 'active']
            );

            $sessionType = $data['session'];
            $startTime = match($sessionType) {
                'full_day' => '09:00:00',
                'half_am' => '09:00:00',
                'half_pm' => '14:00:00',
                default => '09:00:00',
            };
            $endTime = match($sessionType) {
                'full_day' => '17:00:00',
                'half_am' => '13:00:00',
                'half_pm' => '17:00:00',
                default => '17:00:00',
            };

            $booking = Booking::create([
                'booking_id' => $data['booking_id'],
                'case_id' => $case->id,
                'room_id' => $room->id,
                'booking_date' => $bookingDate,
                'session_type' => $sessionType,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'booking_type' => 'external',
                'number_of_attendees' => rand(5, 25),
                'booking_status' => 'confirmed',
                'billing_status' => 'pending',
                'booked_by' => 1,
            ]);

            // Add claimant
            if (isset($contacts[$data['claimant']])) {
                BookingParticipant::create([
                    'booking_id' => $booking->id,
                    'contact_id' => $contacts[$data['claimant']]->id,
                    'role' => 'claimant',
                    'display_order' => 0,
                ]);
            }

            // Add claimant solicitor
            if (isset($contacts[$data['claimant_solicitor']])) {
                BookingParticipant::create([
                    'booking_id' => $booking->id,
                    'contact_id' => $contacts[$data['claimant_solicitor']]->id,
                    'role' => 'claimant_solicitor',
                    'display_order' => 1,
                ]);
            }

            // Add respondent
            if (isset($contacts[$data['respondent']])) {
                BookingParticipant::create([
                    'booking_id' => $booking->id,
                    'contact_id' => $contacts[$data['respondent']]->id,
                    'role' => 'respondent',
                    'display_order' => 2,
                ]);
            }

            // Add respondent solicitor
            if (isset($contacts[$data['respondent_solicitor']])) {
                BookingParticipant::create([
                    'booking_id' => $booking->id,
                    'contact_id' => $contacts[$data['respondent_solicitor']]->id,
                    'role' => 'respondent_solicitor',
                    'display_order' => 3,
                ]);
            }

            // Add arbitrators
            $order = 4;
            foreach ($data['arbitrators'] as $index => $arbName) {
                if (isset($contacts[$arbName])) {
                    BookingParticipant::create([
                        'booking_id' => $booking->id,
                        'contact_id' => $contacts[$arbName]->id,
                        'role' => $index === 0 ? 'presiding_arbitrator' : 'co_arbitrator',
                        'display_order' => $order++,
                    ]);
                }
            }

            // Add features
            foreach ($data['features'] as $featureName) {
                if (isset($features[$featureName])) {
                    BookingFeature::create([
                        'booking_id' => $booking->id,
                        'feature_id' => $features[$featureName]->id,
                        'quantity' => 1,
                    ]);
                }
            }

            $dayOffset++;
        }
    }
}
