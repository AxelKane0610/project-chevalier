<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Out_Of_Office_Tickets_Model extends Model
{
    //
    protected $table = 'out_of_office_tickets';
    protected $fillable = [
        'user_id',
        'type_of_leave',
        'reasons_for_leave',
        'start_date',
        'end_date',
        'status',
        'type_of_leave',
        'days_of_leave'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function user_owner(): BelongsTo
    {
        // Một ticket thì "thuộc về" (belongsTo) một người dùng
        return $this->belongsTo(User::class, 'user_id'); //Bảo model sang model User để lấy thông tin user của ticket đó, dựa vào "user_id"
    }

    public function active_attachments()
    {
        return $this->hasMany(Attachments_Model::class, 'ticket_id', 'id')
            ->where(['type_of_ticket' => 9, 'status' => '1', 'comment_id' => null]); // Chỉ lấy những attachment có status = 1 (còn hiệu lực)
    }

    public function ticket_comments()
    {
        return $this->hasMany(Comments_Model::class, 'ticket_id', 'id') // Liên kết với model Comments_Model, dựa vào "ticket_id" để lấy những comment có ticket_id trùng với id của ticket này
            ->where(['type_of_ticket' => 9]); // Chỉ lấy những comment có type_of_ticket là 3 (laser engraving ticket)
        
    }

    public function ticket_tracking_info()
    {
        return $this->hasMany(tracking_info_model::class, 'ticket_id', 'id') // Liên kết với model tracking_info_model, dựa vào "ticket_id" để lấy những tracking có ticket_id trùng với id của ticket này
            ->where('type_of_ticket', 9);
    }

    public function getStatusDataAttribute()
    {
        return match ($this->status){
            "1" => [
                'text' => 'Open',
                'color' => 'primary'
            ],

            "2" => [
                'text' => 'Waiting for approval',
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

    public function getTypeOfLeaveDataAttribute()
    {
        return match ($this->type_of_leave){
            "1" => [
                'text' => 'Xin nghỉ phép',
                'color' => 'primary'
            ],
            "2" => [
                'text' => 'Xin đi trễ',
                'color' => 'secondary'
            ],
            "3" => [
                'text' => 'Xin về sớm',
                'color' => 'success'
            ],
            "4" => [
                'text' => 'Xin không chấm công vào',
                'color' => 'danger'
            ],
            "5" => [
                'text' => 'Xin không chấm công ra',
                'color' => 'warning'
            ],
            "6" => [
                'text' => 'Quên chấm công vào/ra',
                'color' => 'info'
            ],

            default => [
                'text' => 'Unknown',
                'color' => 'primary'
            ]
        };
    }

    
}
