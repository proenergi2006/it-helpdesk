<?php

namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TicketApprovalRequest extends Mailable
{
    use Queueable, SerializesModels;

    public $ticket;

    public $reviewUrl;

    public function __construct(Ticket $ticket, string $approverEmail)
    {
        $this->ticket = $ticket;
        $this->reviewUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute(
            'approvals.email.show',
            now()->addDays(3),
            ['id' => $ticket->id, 'approver' => $approverEmail]
        );
    }

    public function build()
    {
        return $this->subject("Approval Dibutuhkan: Ticket #{$this->ticket->id}")
            ->view('emails.ticket_approval_request')
            ->text('emails.ticket_approval_request_text');
    }
}
