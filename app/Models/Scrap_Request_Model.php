<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Scrap_Request_Model extends Model
{
    //
    protected $table = 'scrap_request';

    protected $fillable = [
        'user_id',
        'scrap_type',
        'scrap_date',
        'status',
        'scrap_description',
    ];

    public function user_owner(): BelongsTo
    {
        // Một ticket thì "thuộc về" (belongsTo) một người dùng
        return $this->belongsTo(User::class, 'user_id'); //Bảo model sang model User để lấy thông tin user của ticket đó, dựa vào "user_id"
    }

    public function active_attachments()
    {
        return $this->hasMany(Attachments_Model::class, 'ticket_id', 'id')
            ->where(['type_of_ticket' => 14, 'status' => '1', 'comment_id' => null]); // Chỉ lấy những attachment có status = 1 (còn hiệu lực)
    }

    public function ticket_comments()
    {
        return $this->hasMany(Comments_Model::class, 'ticket_id', 'id') 
            ->where(['type_of_ticket' => 14]); 
        
    }

    public function ticket_tracking_info()
    {
        return $this->hasMany(tracking_info_model::class, 'ticket_id', 'id') // Liên kết với model tracking_info_model, dựa vào "ticket_id" để lấy những tracking có ticket_id trùng với id của ticket này
            ->where('type_of_ticket', 14);
    }

    public function getStatusDataAttribute()
    {
        return match ($this->status) {
            "1" => [
                'text' => 'Open',
                'color' => 'primary'
            ],

            "2" => [
                'text' => 'Waiting leader approve',
                'color' => 'secondary'
            ],

            "3" => [
                'text' => 'Waiting manager approve',
                'color' => 'success'
            ],

            "4" => [
                'text' => 'Fully approved',
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
