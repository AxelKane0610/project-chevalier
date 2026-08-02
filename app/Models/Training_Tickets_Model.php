<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Training_Tickets_Model extends Model
{
    //
    protected $table = 'training_tickets_table';

    protected $fillable = [
        'user_id',
        'training_no',
        'status',
        'start_date',
        'end_date',
    ];

    public function user_owner(): BelongsTo
    {
        // Một ticket thì "thuộc về" (belongsTo) một người dùng
        return $this->belongsTo(User::class, 'user_id'); //Bảo model sang model User để lấy thông tin user của ticket đó, dựa vào "user_id"
    }

    public function active_attachments()
    {
        return $this->hasMany(Attachments_Model::class, 'ticket_id', 'id')
            ->where(['type_of_ticket' => 5, 'status' => '1', 'comment_id' => null]); // Chỉ lấy những attachment có status = 1 (còn hiệu lực)
    }

    public function ticket_comments()
    {
        return $this->hasMany(Comments_Model::class, 'ticket_id', 'id') // Liên kết với model Comments_Model, dựa vào "ticket_id" để lấy những comment có ticket_id trùng với id của ticket này
            ->where(['type_of_ticket' => 5]); 
        
    }

    public function ticket_tracking_info()
    {
        return $this->hasMany(tracking_info_model::class, 'ticket_id', 'id') // Liên kết với model tracking_info_model, dựa vào "ticket_id" để lấy những tracking có ticket_id trùng với id của ticket này
            ->where('type_of_ticket', 5);
    }

    public function training_courses(): HasMany
    {
        return $this->hasMany(Training_Courses_Model::class, 'training_no', 'training_no'); //Bảo model sang model Thermal_Event_Exceptional_Tickets_Model để lấy thông tin ticket của part details đó, dựa vào "ticket_id"
    }

    public function getStatusDataAttribute()
    {
        return match ($this->status){
             "1" => [
                'text' => 'Open',
                'color' => 'primary'
            ],

            "2" => [
                'text' => 'Chưa submit',
                'color' => 'secondary'
            ],

            "3" => [
                'text' => 'Đã submit, chờ verify',
                'color' => 'secondary'
            ],

            "4" => [
                'text' => 'Completed',
                'color' => 'success'
            ],

            "5" => [
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
