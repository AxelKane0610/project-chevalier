<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Loan_Unit_Part_Tickets_Model extends Model
{
    //
    protected $table = 'loan_unit_part_tickets';
    
    protected $fillable = [
        'user_id',
        'ticket_receipt',
        'status',
        'customer_unit_info',
    ];

    public function user_owner(): BelongsTo
    {
        // Một ticket thì "thuộc về" (belongsTo) một người dùng
        return $this->belongsTo(User::class, 'user_id'); //Bảo model sang model User để lấy thông tin user của ticket đó, dựa vào "user_id"
    }

    public function active_attachments()
    {
        return $this->hasMany(Attachments_Model::class, 'ticket_id', 'id')
            ->where(['type_of_ticket' => 4, 'status' => '1']); // Chỉ lấy những attachment có status = 1 (còn hiệu lực)
    }

    public function ticket_comments()
    {
        return $this->hasMany(Comments_Model::class, 'ticket_id', 'id') // Liên kết với model Comments_Model, dựa vào "ticket_id" để lấy những comment có ticket_id trùng với id của ticket này
            ->where(['type_of_ticket' => 4]); // Chỉ lấy những comment có type_of_ticket là 1 (software ticket)
        
    }

    public function ticket_tracking_info()
    {
        return $this->hasMany(tracking_info_model::class, 'ticket_id', 'id') // Liên kết với model tracking_info_model, dựa vào "ticket_id" để lấy những tracking có ticket_id trùng với id của ticket này
            ->where('type_of_ticket', 4);
    }

    public function parts_details()
    {
        return $this->hasMany(Loan_Unit_Ticket_Parts_Details_Model::class,'ticket_id','id'); // Liên kết với model Thermal_Event_Parts_Details_Model, dựa vào "ticket_id" để lấy những part details có ticket_id trùng với id của ticket này
    }

    public function getStatusDataAttribute()
    {
        return match ($this->status){
             "1" => [
                'text' => 'Open',
                'class' => 'open'
            ],

            "2" => [
                'text' => 'In Progress',
                'class' => 'in-progress'
            ],

            "3" => [
                'text' => 'Completed',
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
}
