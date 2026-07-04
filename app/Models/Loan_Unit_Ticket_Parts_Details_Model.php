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
        'loan_unit_asset_tag',
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

    public function getStatusDataAttribute()
    {
        return match ($this->status){
             "1" => [
                'text' => 'Requested',
                'class' => 'open'
            ],

            "2" => [
                'text' => 'Borrowed, not return yet',
                'class' => 'in-progress'
            ],

            "3" => [
                'text' => 'Returned',
                'class' => 'completed'
            ],

            "4" => [
                'text' => 'Canceled',
                'class' => 'canceled'
            ],

            default => [
                'text' => 'Unknown',
                'class' => 'unknown'
            ]
        };
    }

    public function getOriginalDataAttribute()
    {
        // Sử dụng getAttribute() để tránh xung đột với thuộc tính hệ thống của Laravel
        return match ($this->getAttribute('original')) {
            "1" => [
                'text' => 'Crown',
                'class' => 'crown'
            ],

            "2" => [
                'text' => 'Spectre',
                'class' => 'spectre'
            ],

            "3" => [
                'text' => 'T1 (FPT, DGW, Elite)',
                'class' => 't1'
            ],

            default => [
                'text' => 'Unknown',
                'class' => 'unknown'
            ]
        };
    }
}
