<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketAttachment extends Model
{
    protected $fillable = ['ticket_id', 'file_name', 'file_path', 'file_size'];
}
