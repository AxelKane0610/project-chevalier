<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Loan_Unit_Ticket_Parts_Details_Model extends Model
{
    //
    protected $table = 'loan_unit_ticket_parts_details';
    protected $fillable = [
        'ticket_id',
        'ticket_receipt',
        'user_id',
        'part_request',
        'status',
        'loan_unit_asset_ta',
        'loan_unit_serial_number',
        'ct_loaned',
        'new_ct_return',
        'original',
        'start_date',
        'end_date',
    ];

    public function ticket_owner(): BelongsTo
    {
        return $this->belongsTo(Loan_Unit_Part_Tickets_Model::class, 'ticket_id', 'id'); 
    }

    public function user_owner(): BelongsTo
    {
        // Một ticket thì "thuộc về" (belongsTo) một người dùng
        return $this->belongsTo(User::class, 'user_id'); //Bảo model sang model User để lấy thông tin user của ticket đó, dựa vào "user_id"
    }
}
