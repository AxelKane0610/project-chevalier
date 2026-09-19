<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class BBNT_Partner_Onsite_Model extends Model
{
    //
    protected $table = 'bbnt_partner_onsite';

    protected $fillable = [
        'id',
        'user_id',
        'submit_date',
        'partner_address',
        'partner_city',
        'onsite_type',
        'document_name',
        'total_case',
        'total_amount',
        'status',
        'notes',
    ];

    public function active_attachments()
    {
        return $this->hasMany(Attachments_Model::class, 'ticket_id', 'id')
            ->where(['type_of_ticket' => 13, 'status' => '1', 'comment_id' => null]); // Chỉ lấy những attachment có status = 1 (còn hiệu lực)
    }

    public function ticket_comments()
    {
        return $this->hasMany(Comments_Model::class, 'ticket_id', 'id')
            ->where(['type_of_ticket' => 13]); 
        
    }

    public function ticket_tracking_info()
    {
        return $this->hasMany(tracking_info_model::class, 'ticket_id', 'id') // Liên kết với model tracking_info_model, dựa vào "ticket_id" để lấy những tracking có ticket_id trùng với id của ticket này
            ->where('type_of_ticket', 13);
    }

    public function user_owner(): BelongsTo
    {
        // Một ticket thì "thuộc về" (belongsTo) một người dùng
        return $this->belongsTo(User::class, 'user_id'); //Bảo model sang model User để lấy thông tin user của ticket đó, dựa vào "user_id"
    }

    public function getStatusDataAttribute()
    {
        return match ($this->status) {
            "1" => [
                'text' => 'Đã submit, chờ kiểm tra',
                'color' => 'primary'
            ],

            "2" => [
                'text' => 'Đang kiểm tra',
                'color' => 'danger'
            ],

            "3" => [
                'text' => 'Hoàn tất',
                'color' => 'success'
            ],


            default => [
                'text' => 'Unknown',
                'color' => 'primary'
            ]
        };
    }

    public function getOnsiteTypeDataAttribute()
    {
        return match ($this->onsite_type) {
            "1" => [
                'text' => 'MÁY TÍNH',
                'color' => 'primary'
            ],

            "2" => [
                'text' => 'MÁY IN',
                'color' => 'secondary'
            ],


            default => [
                'text' => 'Unknown',
                'color' => 'primary'
            ]
        };
    }
}
