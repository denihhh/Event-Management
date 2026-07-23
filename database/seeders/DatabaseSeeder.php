<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\OrganizerApplication;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with rich sample data across all roles & months.
     */
    public function run(): void
    {
        // 1. Core Default System Users
        $admin = User::factory()->create([
            'name' => 'System Admin',
            'email' => 'admin@eventive.test',
            'is_admin' => true,
            'is_organizer' => false,
        ]);

        $organizer = User::factory()->create([
            'name' => 'Apex Events & Sports',
            'email' => 'organizer@eventive.test',
            'is_organizer' => true,
            'is_admin' => false,
        ]);

        $attendee = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'attendee@eventive.test',
            'is_organizer' => false,
            'is_admin' => false,
        ]);

        // 2. Additional Organizers
        $organizer2 = User::factory()->organizer()->create([
            'name' => 'KL Marathon Club',
            'email' => 'klmarathon@eventive.test',
        ]);

        $organizer3 = User::factory()->organizer()->create([
            'name' => 'TechAsia Productions',
            'email' => 'techasia@eventive.test',
        ]);

        // 3. Pool of 60 Registered Attendees
        $attendees = User::factory()->count(60)->create();

        // 4. Realistic Events for Apex Events & Sports ($organizer)
        $apexEvents = [
            [
                'title' => 'KL City Night Run 2026',
                'description' => 'Experience the vibrant pulse of Kuala Lumpur under the stars! Featuring 5KM Fun Run, 10KM Competitive, and 21KM Half Marathon categories with runner entitlements.',
                'starts_at' => now()->addDays(14)->setTime(20, 0),
                'location' => 'Dataran Merdeka, Kuala Lumpur',
                'capacity' => 500,
            ],
            [
                'title' => 'Cyberjaya AI & Tech Summit 2026',
                'description' => 'Southeast Asia premier technology conference bringing together software engineers, product managers, and AI researchers for two days of keynotes.',
                'starts_at' => now()->addDays(25)->setTime(9, 0),
                'location' => 'Cyberjaya Convention Centre, Selangor',
                'capacity' => 400,
            ],
            [
                'title' => 'Langkawi International Triathlon 2026',
                'description' => 'Ocean swim, coastal cycling, and island marathon in tropical Langkawi island.',
                'starts_at' => now()->addDays(45)->setTime(7, 0),
                'location' => 'Pantai Cenang, Langkawi',
                'capacity' => 600,
            ],
            [
                'title' => 'Annual Developer & Open Source Conference',
                'description' => 'Community gathering sharing insights on Laravel, Livewire, and modern web application development.',
                'starts_at' => now()->subDays(15)->setTime(10, 0),
                'location' => 'KL Sentral Auditorium, Kuala Lumpur',
                'capacity' => 300,
            ],
        ];

        $createdApexEvents = [];
        foreach ($apexEvents as $data) {
            $createdApexEvents[] = Event::create([
                'user_id' => $organizer->id,
                ...$data,
                'tickets_sold' => 0,
            ]);
        }

        // 5. Additional Events for Other Organizers
        $otherEvents = [
            [
                'user_id' => $organizer2->id,
                'title' => 'Penang Heritage Trail Walk & Food Fest',
                'description' => 'A weekend celebration of Penang world-famous heritage and street culinary delights.',
                'starts_at' => now()->addDays(7)->setTime(8, 30),
                'location' => 'George Town, Penang',
                'capacity' => 100,
            ],
            [
                'user_id' => $organizer2->id,
                'title' => 'Selangor Green Marathon 2026',
                'description' => 'Run for sustainability! 100% eco-friendly marathon promoting zero-plastic waste.',
                'starts_at' => now()->addHours(18)->setTime(6, 30),
                'location' => 'Taman Botani Negara, Shah Alam',
                'capacity' => 200,
            ],
            [
                'user_id' => $organizer3->id,
                'title' => 'Borneo Wilderness Ultra Trail 2026',
                'description' => 'Conquer the rugged trails of Sabah! Extreme 50KM and 100KM ultra-marathon.',
                'starts_at' => now()->addMonths(2)->setTime(5, 0),
                'location' => 'Kundasang, Sabah',
                'capacity' => 80,
            ],
        ];

        foreach ($otherEvents as $data) {
            Event::create([
                ...$data,
                'tickets_sold' => 0,
            ]);
        }

        // 6. Give John Doe (attendee@eventive.test) tickets for Apex Events
        foreach (array_slice($createdApexEvents, 0, 3) as $event) {
            Ticket::create([
                'event_id' => $event->id,
                'user_id' => $attendee->id,
                'ticket_code' => (string) Str::uuid(),
                'reminder_sent' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $event->increment('tickets_sold');
        }

        // 7. Generate Rich Historical Monthly Ticket Registrations (Jan - Dec) for Apex Events ($organizer)
        $currentYear = now()->year;
        $monthlyTicketTargets = [
            1 => 12,  // Jan
            2 => 19,  // Feb
            3 => 15,  // Mar
            4 => 28,  // Apr
            5 => 24,  // May
            6 => 36,  // Jun
            7 => 42,  // Jul
            8 => 58,  // Aug
            9 => 49,  // Sep
            10 => 64, // Oct
            11 => 78, // Nov
            12 => 92, // Dec
        ];

        foreach ($monthlyTicketTargets as $month => $targetCount) {
            $createdForMonth = 0;
            $shuffledAttendees = $attendees->shuffle();

            foreach ($shuffledAttendees as $randomAttendee) {
                if ($createdForMonth >= $targetCount) {
                    break;
                }

                $targetEvent = $createdApexEvents[$createdForMonth % count($createdApexEvents)];

                if (! Ticket::where('event_id', $targetEvent->id)->where('user_id', $randomAttendee->id)->exists()) {
                    $randomDay = rand(1, 28);
                    $ticketCreatedAt = Carbon::create($currentYear, $month, $randomDay, rand(8, 22), rand(0, 59));

                    Ticket::create([
                        'event_id' => $targetEvent->id,
                        'user_id' => $randomAttendee->id,
                        'ticket_code' => (string) Str::uuid(),
                        'reminder_sent' => $targetEvent->starts_at->isPast(),
                        'created_at' => $ticketCreatedAt,
                        'updated_at' => $ticketCreatedAt,
                    ]);

                    $targetEvent->increment('tickets_sold');
                    $createdForMonth++;
                }
            }
        }

        // 8. Organizer Applications (for Admin Review portal)
        $applicant1 = User::factory()->create(['name' => 'Sarah Connor', 'email' => 'sarah@example.com']);
        OrganizerApplication::create([
            'user_id' => $applicant1->id,
            'organization_name' => 'Vanguard Fitness & Endurance',
            'business_reg_number' => '202401088712 (1452391-V)',
            'contact_phone' => '+60 12-888 9911',
            'bank_name' => 'Maybank',
            'bank_account_number' => '514012998877',
            'event_title' => 'Genting Highlands Trail Climb 2026',
            'event_description' => 'Challenging 15KM hill climb race from Awana to SkyCentral with 1,200m elevation gain.',
            'proposed_starts_at' => now()->addDays(40),
            'status' => 'pending',
        ]);

        $applicant2 = User::factory()->create(['name' => 'Michael Tan', 'email' => 'michael@example.com']);
        OrganizerApplication::create([
            'user_id' => $applicant2->id,
            'organization_name' => 'Asia E-Sports League Sdn Bhd',
            'business_reg_number' => '202301044512 (1298451-E)',
            'contact_phone' => '+60 17-555 1234',
            'bank_name' => 'CIMB Bank',
            'bank_account_number' => '8009123456',
            'event_title' => 'Malaysia Championship Gaming Expo 2026',
            'event_description' => '3-day esports tournament featuring Valorant, Mobile Legends, and Tekken 8 live finals.',
            'proposed_starts_at' => now()->addDays(60),
            'status' => 'pending',
        ]);

        $applicant3 = User::factory()->create(['name' => 'David Lee', 'email' => 'david@example.com']);
        OrganizerApplication::create([
            'user_id' => $applicant3->id,
            'organization_name' => 'Unverified Hobbyist Group',
            'business_reg_number' => 'N/A',
            'contact_phone' => '+60 11-1234 5678',
            'bank_name' => 'RHB Bank',
            'bank_account_number' => '1120019988',
            'event_title' => 'Pop-up Street Race',
            'event_description' => 'Informal street race around industrial park.',
            'proposed_starts_at' => now()->addDays(15),
            'status' => 'rejected',
            'admin_notes' => 'Application rejected due to lack of official SSM business registration and safety permits.',
        ]);
    }
}
