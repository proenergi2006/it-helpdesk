<?php

namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TicketApprovalResultUser extends Mailable
{
    use Queueable, SerializesModels;

    public $ticket;

    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    public function build()
    {
        $hasil = $this->ticket->status === 'rejected' ? 'Ditolak' : 'Disetujui';

        return $this->subject("Ticket #{$this->ticket->id} {$hasil}")
            ->view('emails.ticket_approval_result_user');
    }
}
