<?php

namespace App\Console\Commands;

use App\Mail\TicketApprovalRequest;
use App\Models\Ticket;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestApprovalMail extends Command
{
    protected $signature = 'helpdesk:test-approval-mail {email? : Tujuan uji (default: semua approver)}';

    protected $description = 'Kirim email approval uji (memakai tiket terakhir) dan tampilkan error SMTP jika gagal.';

    public function handle(): int
    {
        $this->line('Mailer   : '.config('mail.default'));
        $this->line('Host     : '.config('mail.mailers.smtp.host').':'.config('mail.mailers.smtp.port'));
        $this->line('From     : '.config('mail.from.address'));
        $this->line('APP_URL  : '.config('app.url'));
        $this->line('Approver : '.implode(', ', (array) config('helpdesk.approvers', [])));

        $ticket = Ticket::latest('id')->first();
        if (!$ticket) {
            $this->error('Belum ada tiket untuk dipakai sebagai contoh.');

            return self::FAILURE;
        }

        $targets = $this->argument('email') ? [$this->argument('email')] : (array) config('helpdesk.approvers', []);
        $failed = false;

        foreach ($targets as $to) {
            try {
                Mail::to($to)->send(new TicketApprovalRequest($ticket, $to));
                $this->info("Terkirim ke {$to} (tiket #{$ticket->id}). Jika tidak sampai, cek folder spam atau karantina.");
            } catch (\Throwable $e) {
                $failed = true;
                $this->error("GAGAL ke {$to}: ".$e->getMessage());
            }
        }

        return $failed ? self::FAILURE : self::SUCCESS;
    }
}
