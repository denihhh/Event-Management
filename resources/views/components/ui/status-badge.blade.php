@props([
    'status' => 'active',
    'event' => null,
    'size' => 'sm',
])

@php
    if ($event) {
        if (method_exists($event, 'trashed') && $event->trashed()) {
            $status = 'cancelled';
        } elseif ($event->hasEnded()) {
            $status = 'concluded';
        } elseif ($event->hasStarted()) {
            $status = 'in_progress';
        } elseif ($event->isSoldOut()) {
            $status = 'sold_out';
        } else {
            $status = 'active';
        }
    }

    $badgeConfig = match (mb_strtolower((string) $status)) {
        'concluded', 'past' => ['color' => 'zinc', 'label' => __('Concluded')],
        'in_progress' => ['color' => 'teal', 'label' => __('In Progress')],
        'sold_out' => ['color' => 'amber', 'label' => __('Sold Out')],
        'active', 'upcoming' => ['color' => 'emerald', 'label' => __('Active')],
        'checked_in' => ['color' => 'teal', 'label' => __('Checked In')],
        'cancelled', 'trashed' => ['color' => 'red', 'label' => __('Cancelled')],
        'admin' => ['color' => 'purple', 'label' => __('Admin')],
        'organizer' => ['color' => 'indigo', 'label' => __('Organizer')],
        default => ['color' => 'zinc', 'label' => ucfirst((string) $status)],
    };
@endphp

<flux:badge :size="$size" :color="$badgeConfig['color']" rounded {{ $attributes }}>
    {{ $slot->isEmpty() ? $badgeConfig['label'] : $slot }}
</flux:badge>
