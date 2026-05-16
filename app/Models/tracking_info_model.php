<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class tracking_info_model extends Model
{
    //
    use HasFactory;
    protected $table = 'tracking_info';
    
    protected $fillable = [
        'user_id',
        'ticket_id',
        'type_of_ticket',
        'action',
    ];


}
