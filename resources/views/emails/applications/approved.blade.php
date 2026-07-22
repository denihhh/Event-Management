<x-mail::message>
# 🎉 Application Approved!

Hello {{ $user->name }},

We are excited to inform you that your application to become an **Event Organizer** on **{{ config('app.name') }}** has been **Approved** by our team!

**Organization / Business Name:** {{ $application->organization_name ?: $user->name }}<br>
**Proposed Event:** {{ $application->event_title }}

You now have full access to create, publish, manage events, view attendee rosters, and export registration reports.

<x-mail::button :url="route('organizer.events.create')">
Create Your First Event
</x-mail::button>

Welcome aboard,<br>
The {{ config('app.name') }} Team
</x-mail::message>
