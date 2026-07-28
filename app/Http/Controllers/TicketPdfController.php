<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TicketPdfController extends Controller
{
    use AuthorizesRequests;

    /**
     * Generate and download a PDF ticket pass.
     */
    public function download(Request $request, Ticket $ticket): Response
    {
        $this->authorize('download', $ticket);

        $ticket->load(['event.organizer', 'user']);

        $pdf = Pdf::loadView('pdf.ticket-pass', [
            'ticket' => $ticket,
        ]);

        return $pdf->download("Ticket-Pass-{$ticket->ticket_code}.pdf");
    }

    public function upload(Request $reuest, File $file): Response
    {
        $this->authorize('upload', $file);

        $file->load(['event.organizer','user']);

        $pdf = Pdf::loadView('pdf.ticket-pass', [
            'file' => $file,
        ]);

        return $pdf->download("Ticket-Pass-".$file->file_name.".pdf");
    }
}
