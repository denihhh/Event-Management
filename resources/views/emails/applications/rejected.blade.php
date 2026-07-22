<x-mail::message>
# Update on Your Organizer Application

Hello {{ $user->name }},

Thank you for your interest in becoming an Event Organizer on **{{ config('app.name') }}**.

After reviewing your application for **"{{ $application->organization_name ?: $application->event_title }}"**, our team was unable to approve your request at this time.

@if ($application->admin_notes)
### Admin Notes:
> {{ $application->admin_notes }}
@endif

You may update your business credentials and submit a new application at any time.

<x-mail::button :url="route('become-organizer.edit')">
Update Application
</x-mail::button>

Best regards,<br>
The {{ config('app.name') }} Team
</x-mail::message>
