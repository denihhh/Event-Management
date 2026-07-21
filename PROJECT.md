# Project Overview: Event Management & Ticketing System (`eventive`)

## 1. Executive Summary
A web application designed for event organizers to publish public events with fixed ticket capacities and for users to discover, register, and receive custom digital tickets via email with automated reminders.

---

## 2. Tech Stack & Environment
- **Framework:** Laravel 13 (PHP 8.4)
- **Frontend / Auth:** Laravel Breeze with Livewire v3 (Class-Based Components) & Flux UI
- **Styling:** Tailwind CSS v3
- **Testing:** Pest PHP
- **Database:** SQLite (Local) / MySQL (Production)
- **Queue System:** Database Driver (`QUEUE_CONNECTION=database`)
- **Mail:** Laravel Mailable (`EventTicketMail`) via Mailtrap / SMTP
- **AI Tooling Integration:** Laravel Boost MCP Server

---

## 3. Core Domain Models & Relationships

### Users (`users`)
- Organizers (can create and manage events)
- Attendees (can browse and register for tickets)

### Events (`events`)
- Belongs to a User (Organizer)
- Has many Tickets (`tickets()`)
- **Attributes:** `title`, `description`, `starts_at`, `location`, `capacity`, `tickets_sold`

### Tickets (`tickets`)
- Belongs to an Event (`event()`)
- Belongs to a User (Attendee) (`user()`)
- **Attributes:** `ticket_code` (UUID/Unique key string), `reminder_sent` (boolean / timestamp)

---

## 4. Key Functional Features & Architecture

### A. Event Management (Organizers)
- Create, update, and publish public events.
- Set maximum capacity constraints.
- Real-time display of remaining ticket availability.

### B. Registration & Digital Ticketing (Attendees)
- Public event discovery and detail views.
- One-click ticket registration for authenticated users.
- **Concurrency Prevention:** Wrap registration inside `DB::transaction()` with pessimistic locking (`lockForUpdate()`) on the Event record to guarantee capacity is never exceeded.
- Dispatch queued email (`EventTicketMail`) containing digital ticket credentials upon successful registration (`ShouldQueue`).

### C. Automated Reminders
- Custom Artisan command (`app:send-event-reminders`) scheduled via `routes/console.php`.
- Evaluates upcoming events starting within 24 hours where `reminder_sent` is `false`.
- Queues reminder emails (`EventReminderMail`) to registered attendees and sets `reminder_sent = true`.

---

## 5. Business Rules & Technical Constraints
1. **Capacity Guarantee:** Ticket registrations must use pessimistic database locking (`lockForUpdate()`) inside transactions to handle concurrent purchases safely.
2. **Asynchronous Execution:** All mail notifications (`EventTicketMail`, `EventReminderMail`) must implement `ShouldQueue` and run on the database queue driver.
3. **Route Security:** Event creation/management routes must be restricted to authenticated users via `auth` middleware.
4. **Testing Standards:** All feature and unit tests must be written using Pest PHP (`test()` / `it()` syntax).
5. **Component Structure:** Use standard Class-based Livewire components (`app/Livewire/*` + `resources/views/livewire/*`).