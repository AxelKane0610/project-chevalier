<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comments_Model extends Model
{
    //
    use HasFactory;
    protected $table = 'comments_table';

    protected $fillable = [
        'id',
        'ticket_id',
        'type_of_ticket',
        'user_id',
        'comment',
    ];
}
