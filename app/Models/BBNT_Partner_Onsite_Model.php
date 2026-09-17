<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BBNT_Partner_Onsite_Model extends Model
{
    //
    protected $table = 'bbnt_partner_onsite';

    protected $fillable = [
        'id',
        'user_id',
        'submit_date',
        'requester_name',
        'partner_address',
        'partner_city',
        'onsite_type',
        'document_name',
        'total_case',
        'total_amount',
        'status',
        'notes',
    ];
}
