<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\OrganizerApplication;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
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

        // 3. Additional Attendees (15 users)
        $attendees = User::factory()->count(15)->create();

        // 4. Realistic Events
        $eventsData = [
            [
                'user_id' => $organizer->id,
                'title' => 'KL City Night Run 2026',
                'description' => 'Experience the vibrant pulse of Kuala Lumpur under the stars! Featuring 5KM Fun Run, 10KM Competitive, and 21KM Half Marathon categories with runner entitlements, t-shirt, and finisher medal.',
                'starts_at' => now()->addDays(14)->setTime(20, 0),
                'location' => 'Dataran Merdeka, Kuala Lumpur',
                'capacity' => 150,
            ],
            [
                'user_id' => $organizer->id,
                'title' => 'Cyberjaya AI & Tech Summit 2026',
                'description' => 'Southeast Asia\'s premier technology conference bringing together software engineers, product managers, and AI researchers for two days of keynote talks and interactive workshops.',
                'starts_at' => now()->addDays(25)->setTime(9, 0),
                'location' => 'Cyberjaya Convention Centre, Selangor',
                'capacity' => 80,
            ],
            [
                'user_id' => $organizer2->id,
                'title' => 'Penang Heritage Trail Walk & Food Fest',
                'description' => 'A weekend celebration of Penang\'s world-famous heritage and street culinary delights. Includes guided heritage walking passes and food voucher booklets.',
                'starts_at' => now()->addDays(7)->setTime(8, 30),
                'location' => 'George Town, Penang',
                'capacity' => 100,
            ],
            [
                'user_id' => $organizer2->id,
                'title' => 'Selangor Green Marathon 2026',
                'description' => 'Run for sustainability! 100% eco-friendly marathon promoting zero-plastic waste and mangrove tree planting initiatives for every runner ticket registered.',
                'starts_at' => now()->addHours(18)->setTime(6, 30), // Starting within 24 hours for reminder test!
                'location' => 'Taman Botani Negara, Shah Alam',
                'capacity' => 200,
            ],
            [
                'user_id' => $organizer3->id,
                'title' => 'Borneo Wilderness Ultra Trail 2026',
                'description' => 'Conquer the rugged trails of Sabah! Extreme 50KM and 100KM ultra-marathon through pristine rainforests and mountain ridges.',
                'starts_at' => now()->addMonths(2)->setTime(5, 0),
                'location' => 'Kundasang, Sabah',
                'capacity' => 50,
            ],
            [
                'user_id' => $organizer3->id,
                'title' => 'Annual Developer & Open Source Meetup 2025',
                'description' => 'Past community developer gathering sharing insights on Laravel, Livewire, and modern web application development.',
                'starts_at' => now()->subDays(10)->setTime(14, 0), // Past event
                'location' => 'KL Sentral Auditorium, Kuala Lumpur',
                'capacity' => 60,
            ],
        ];

        $createdEvents = [];
        foreach ($eventsData as $data) {
            $createdEvents[] = Event::create([
                ...$data,
                'tickets_sold' => 0,
            ]);
        }

        // 5. Register Tickets
        // Give John Doe (attendee@eventive.test) tickets for the first 3 events
        foreach (array_slice($createdEvents, 0, 3) as $event) {
            Ticket::create([
                'event_id' => $event->id,
                'user_id' => $attendee->id,
                'ticket_code' => (string) Str::uuid(),
                'reminder_sent' => false,
            ]);
            $event->increment('tickets_sold');
        }

        // Register random attendees into various events
        foreach ($createdEvents as $event) {
            // Pick 3-8 random attendees per event
            $eventAttendees = $attendees->random(rand(3, 8));
            foreach ($eventAttendees as $user) {
                // Ensure unique ticket
                if (! Ticket::where('event_id', $event->id)->where('user_id', $user->id)->exists()) {
                    Ticket::create([
                        'event_id' => $event->id,
                        'user_id' => $user->id,
                        'ticket_code' => (string) Str::uuid(),
                        'reminder_sent' => $event->starts_at->isPast(),
                    ]);
                    $event->increment('tickets_sold');
                }
            }
        }

        // 6. Organizer Applications (for Admin Review portal)
        // Pending application 1
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

        // Pending application 2
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

        // Rejected application
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
