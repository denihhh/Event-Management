<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ticket Pass - {{ $ticket->ticket_code }}</title>

    <style>
        @page {
            margin: 20px;
            size: A4 portrait;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #18181b;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
            font-size: 13px;
            line-height: 1.4;
        }

        .ticket-wrapper {
            border: 2px solid #7e22ce;
            border-radius: 12px;
            overflow: hidden;
            background-color: #ffffff;
            max-width: 700px;
            margin: 0 auto;
        }

        .ticket-header {
            background-color: #581c87;
            color: #ffffff;
            padding: 16px 24px;
        }

        .ticket-header table {
            width: 100%;
        }

        .brand-title {
            font-size: 20px;
            font-weight: bold;
            color: #ffffff;
            letter-spacing: 0.5px;
        }

        .brand-sub {
            font-size: 11px;
            color: #e9d5ff;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            text-align: center;
        }

        .badge-active { background-color: #10b981; color: #ffffff; }
        .badge-checked { background-color: #0d9488; color: #ffffff; }
        .badge-cancelled { background-color: #ef4444; color: #ffffff; }
        .badge-past { background-color: #71717a; color: #ffffff; }

        .ticket-body {
            padding: 24px;
        }

        .event-title {
            font-size: 22px;
            font-weight: bold;
            color: #09090b;
            margin-bottom: 12px;
        }

        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .meta-table td {
            padding: 6px 0;
            vertical-align: top;
        }

        .meta-label {
            width: 120px;
            font-size: 11px;
            font-weight: bold;
            color: #71717a;
            text-transform: uppercase;
        }

        .meta-value {
            font-size: 13px;
            font-weight: 500;
            color: #27272a;
        }

        .divider {
            border-top: 1px dashed #e4e4e7;
            margin: 20px 0;
        }

        .pass-box {
            background-color: #f3e8ff;
            border: 1px solid #d8b4fe;
            border-radius: 10px;
            padding: 16px;
        }

        .pass-table {
            width: 100%;
        }

        .ticket-code {
            font-family: 'Courier New', Courier, monospace;
            font-size: 14px;
            font-weight: bold;
            color: #581c87;
            letter-spacing: 1px;
        }

        .qr-container {
            text-align: right;
        }

        .qr-image {
            width: 110px;
            height: 110px;
            background-color: #ffffff;
            padding: 6px;
            border: 1px solid #d8b4fe;
            border-radius: 8px;
        }

        .ticket-footer {
            background-color: #fafafa;
            border-top: 1px solid #f4f4f5;
            padding: 12px 24px;
            font-size: 10px;
            color: #a1a1aa;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="ticket-wrapper">
        <!-- Header -->
        <div class="ticket-header">
            <table>
                <tr>
                    <td>
                        <div class="brand-title">Eventive</div>
                        <div class="brand-sub">Official Digital Entry Pass</div>
                    </td>
                    <td style="text-align: right;">
                        @if ($ticket->isCancelled())
                            <span class="status-badge badge-cancelled">Cancelled</span>
                        @elseif ($ticket->isCheckedIn())
                            <span class="status-badge badge-checked">Checked In</span>
                        @elseif ($ticket->event?->hasStarted())
                            <span class="status-badge badge-past">Past Event</span>
                        @else
                            <span class="status-badge badge-active">Active Pass</span>
                        @endif
                    </td>
                </tr>
            </table>
        </div>

        <!-- Body -->
        <div class="ticket-body">
            <div class="event-title">{{ $ticket->event?->title ?? 'Event Pass' }}</div>

            <table class="meta-table">
                <tr>
                    <td class="meta-label">Organizer</td>
                    <td class="meta-value">{{ $ticket->event?->organizer?->name ?? 'Event Organizer' }}</td>
                </tr>
                <tr>
                    <td class="meta-label">Date & Time</td>
                    <td class="meta-value">{{ $ticket->event?->starts_at?->format('l, F j, Y \a\t g:i A') }}</td>
                </tr>
                <tr>
                    <td class="meta-label">Location</td>
                    <td class="meta-value">{{ $ticket->event?->location ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="meta-label">Attendee Name</td>
                    <td class="meta-value">{{ $ticket->user?->name ?? 'Attendee' }}</td>
                </tr>
                <tr>
                    <td class="meta-label">Attendee Email</td>
                    <td class="meta-value">{{ $ticket->user?->email ?? 'N/A' }}</td>
                </tr>
            </table>

            <!-- Pass & QR Code Section -->
            <div class="pass-box">
                <table class="pass-table">
                    <tr>
                        <td style="vertical-align: middle;">
                            <div style="font-size: 10px; text-transform: uppercase; font-weight: bold; color: #7e22ce; margin-bottom: 4px;">Ticket Identifier Code</div>
                            <div class="ticket-code">{{ $ticket->ticket_code }}</div>
                            <div style="font-size: 10px; color: #6b21a8; margin-top: 6px;">
                                Claimed on {{ $ticket->created_at->format('M j, Y \a\t g:i A') }}
                            </div>
                        </td>
                        <td class="qr-container" style="width: 120px;">
                            <img src="{{ $ticket->getQrCodeDataUri() }}" alt="Ticket QR Code" class="qr-image">
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Footer Notice -->
        <div class="ticket-footer">
            Present this PDF pass or mobile QR code at venue door check-in. Generated by Eventive Platform.
        </div>
    </div>
</body>
</html>
