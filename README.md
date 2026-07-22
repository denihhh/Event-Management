# 🎪 Eventive — Event Management System

[![Laravel](https://img.shields.io/badge/Laravel-v13-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Livewire](https://img.shields.io/badge/Livewire-v4-4E56A6?style=for-the-badge&logo=livewire&logoColor=white)](https://livewire.laravel.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-v4-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white)](https://tailwindcss.com)
[![Pest](https://img.shields.io/badge/Pest-v4-28A745?style=for-the-badge&logo=pest&logoColor=white)](https://pestphp.com)

**Eventive** is a modern, high-performance Event Management System built on **Laravel 13**, **Livewire 4**, and **Flux UI**. It provides a complete end-to-end platform for event discovery, digital ticket issuance, organizer business application workflows, attendee roster management, and administrator user impersonation.

---

## Key Features

### 1. Digital Ticketing & Attendance (Attendees)
* **Event Discovery**: Search and filter public events by date (`upcoming`, `this_week`, `this_month`, `past`).
* **One-Click Pass Claiming**: Claim free digital tickets with instant UUID ticket code generation.
* **Ticket Wallet (`MyTickets`)**: View active passes and cancel reservations anytime.

### 2. Event Operations (Organizers)
* **Event Lifecycle Management**: Publish, edit, and soft-delete events with capacity enforcement.
* **Attendee Roster (`GlobalAttendees`)**: Real-time search by attendee name, email, or ticket code.
* **Streaming CSV Export**: Export complete attendee lists to CSV for check-in desk operations.
* **Automated Attendee Notifications**: Automatically email attendees when an event is updated or cancelled.

### 3. Business Application & Review Workflow
* **Partner Application**: Attendees can submit legal business credentials (SSM Registration No., Phone, Bank Payout Account) and event proposals.
* **Admin Review Portal**: Admins evaluate partner applications and grant permanent organizer status with automated email notifications.
* **Duplicate Submission Guard**: Prevents attendees from spam-submitting multiple pending requests.

### 4. System Administration & Security
* **User Management & Impersonation**: Built-in account impersonation powered by `lab404/laravel-impersonate`. Admins can inspect the system from any user's perspective without altering credentials.
* **Pessimistic Concurrency Locking**: Uses `DB::transaction()` with `lockForUpdate()` on event rows to prevent ticket overselling during registration rushes.
* **Automated Reminders**: Hourly scheduled Artisan command (`app:send-event-reminders`) using Eloquent `cursor()` to dispatch reminder emails 24 hours prior to event start.

---

## Technology Stack

| Component | Technology |
|-----------|------------|
| **Backend Framework** | Laravel 13 (PHP 8.4) |
| **Reactive Frontend** | Livewire 4 & Flux UI v2 |
| **Styling** | Tailwind CSS v4 |
| **Authentication** | Laravel Fortify & `laravel-impersonate` |
| **Testing** | Pest PHP v4 (128+ Feature Tests) |
| **Code Formatting** | Laravel Pint |

---

## Quick Start Guide

### Prerequisites
* **PHP** `>= 8.4`
* **Composer** `>= 2.x`
* **Node.js** `>= 20.x` & **npm**
* **MySQL** or **Laravel Herd**

### 1. Clone & Install Dependencies
```bash
git clone https://github.com/your-username/eventive.git
cd eventive

composer install
npm install
```

### 2. Environment Configuration
```bash
cp .env.example .env
php artisan key:generate
```

Configure your database connection in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=eventive
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Database Migration & Seeding
Populate the database with fresh schema and demo accounts:
```bash
php artisan migrate:fresh --seed
```

#### Pre-Configured Demo Accounts (Default Password: `password`)
| Role | Email | Capabilities |
|------|-------|--------------|
| **System Admin** | `admin@eventive.test` | System audit, user impersonation, partner review |
| **Organizer** | `organizer@eventive.test` | Event creation, roster management, CSV export |
| **Attendee** | `attendee@eventive.test` | Browse events, claim tickets, view pass wallet |

### 4. Serve the Application
```bash
# Terminal 1: Run Vite dev server
npm run dev

# Terminal 2: Run Artisan serve (or use Laravel Herd at http://eventive.test)
php artisan serve
```

---

## Running Automated Tests

Eventive has a comprehensive test suite powered by **Pest PHP**:

```bash
# Run all tests
php artisan test --compact

# Run a specific test file
php artisan test --compact --filter=OrganizerApplicationTest

# Run code style formatting check
vendor/bin/pint --dirty --format agent
```

---

## Project Structure

```text
app/
├── Console/
│   └── Commands/SendEventReminders.php    # Hourly event reminder scheduler
├── Http/
│   └── Middleware/EnsureUserIsOrganizer.php # Role access guard
├── Livewire/
│   ├── Admin/                              # User Management, Applications Review, System Status
│   ├── Events/                             # Public Event Index & Details
│   ├── Organizer/                          # Event Management, Attendee Rosters
│   ├── Settings/                           # Become Organizer Application Form
│   └── Tickets/                            # Ticket Wallet & Registration Modal
├── Mail/                                   # Ticket, Reminder, & Application Status Mailables
├── Models/                                 # User, Event, Ticket, OrganizerApplication
└── Policies/                               # EventPolicy (Authorization rules)
```

---

## License

The Eventive application is open-sourced software licensed under the [MIT license](LICENSE).
