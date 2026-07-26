<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Laser_Engraving_Tickets_Model extends Model
{
    //
    protected $table = 'laser_engraving_tickets';
    protected $fillable = [
        'user_id',
        'ticket_receipt',
        'status',
        'priority',
        'info_base',
        'barcode_info',
        'description',
    ];

    public function user_owner(): BelongsTo
    {
        // Một ticket thì "thuộc về" (belongsTo) một người dùng
        return $this->belongsTo(User::class, 'user_id'); //Bảo model sang model User để lấy thông tin user của ticket đó, dựa vào "user_id"
    }

    public function active_attachments()
    {
        return $this->hasMany(Attachments_Model::class, 'ticket_id', 'id')
            ->where(['type_of_ticket' => 3, 'status' => '1', 'comment_id' => null]); // Chỉ lấy những attachment có status = 1 (còn hiệu lực)
    }

    public function ticket_comments()
    {
        return $this->hasMany(Comments_Model::class, 'ticket_id', 'id') // Liên kết với model Comments_Model, dựa vào "ticket_id" để lấy những comment có ticket_id trùng với id của ticket này
            ->where(['type_of_ticket' => 3]); // Chỉ lấy những comment có type_of_ticket là 3 (laser engraving ticket)
        
    }

    public function ticket_tracking_info()
    {
        return $this->hasMany(tracking_info_model::class, 'ticket_id', 'id') // Liên kết với model tracking_info_model, dựa vào "ticket_id" để lấy những tracking có ticket_id trùng với id của ticket này
            ->where('type_of_ticket', 3);
    }

    public function getPriorityDataAttribute()
    {
        return match($this->priority){
            "1" => [
                'text' => 'Normal',
                'color' => 'success'
            ],

            "2" => [
                'text' => 'Critical',
                'color' => 'danger'
            ],

            "3" => [
                'text' => 'High',
                'color' => 'warning'
            ],

            "4" => [
                'text' => 'Low',
                'color' => 'primary'
            ],

            default => [
                'text' => 'Unknown',
                'color' => 'primary'
            ]
        };
    }

    public function getStatusDataAttribute()
    {
        return match ($this->status){
             "1" => [
                'text' => 'Not started',
                'color' => 'primary'
            ],

            "2" => [
                'text' => 'In Progress',
                'color' => 'secondary'
            ],

            "3" => [
                'text' => 'Completed',
                'color' => 'success'
            ],

            "4" => [
                'text' => 'Rejected',
                'color' => 'danger'
            ],



            default => [
                'text' => 'Unknown',
                'color' => 'primary'
            ]
        };
    }
}
