<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TTEX_Tickets_Model extends Model
{
    //
    protected $table = 'ttex_tickets';

    protected $fillable = [
        'user_id',
        'ttex_bill',
        'category',
        'shipment_type',
        'part_status',
        'status',
        'part_return_deadline',
        'sender_info',
        'receiver_info',
        'shipment_description',
        'note',
        'booking_date',
    ];

    public function user_owner(): BelongsTo
    {
        // Một ticket thì "thuộc về" (belongsTo) một người dùng
        return $this->belongsTo(User::class, 'user_id'); //Bảo model sang model User để lấy thông tin user của ticket đó, dựa vào "user_id"
    }

    public function active_attachments()
    {
        return $this->hasMany(Attachments_Model::class, 'ticket_id', 'id')
            ->where(['type_of_ticket' => 2, 'status' => '1', 'comment_id' => null]); // Chỉ lấy những attachment có status = 1 (còn hiệu lực)
    }

    public function ticket_comments()
    {
        return $this->hasMany(Comments_Model::class, 'ticket_id', 'id') // Liên kết với model Comments_Model, dựa vào "ticket_id" để lấy những comment có ticket_id trùng với id của ticket này
            ->where(['type_of_ticket' => 2]); // Chỉ lấy những comment có type_of_ticket là 10 (thermal event exceptional ticket)
        
    }

    public function ticket_tracking_info()
    {
        return $this->hasMany(tracking_info_model::class, 'ticket_id', 'id') // Liên kết với model tracking_info_model, dựa vào "ticket_id" để lấy những tracking có ticket_id trùng với id của ticket này
            ->where('type_of_ticket', 2);
    }

    public function getStatusDataAttribute()
    {
        return match ($this->status){
             "1" => [
                'text' => 'Open - Chưa điều tin',
                'color' => 'primary'
            ],

            "2" => [
                'text' => 'Completed - Đã điều tin',
                'color' => 'success'
            ],

            "3" => [
                'text' => 'Rejected',
                'color' => 'danger'
            ],



            default => [
                'text' => 'Unknown',
                'color' => 'primary'
            ]
        };
    }

    public function getPartStatusDataAttribute()
    {
        return match ($this->part_status){
             "1" => [
                'text' => 'Good part',
                'color' => 'success'
            ],

            "2" => [
                'text' => 'Def part',
                'color' => 'danger'
            ],

            "3" => [
                'text' => 'Good part - Unused',
                'color' => 'danger'
            ],



            default => [
                'text' => 'Unknown',
                'color' => 'primary'
            ]
        };
    }

    public function getCategoryDataAttribute()
    {
        return match ($this->category){
             "1" => [
                'text' => 'ASRC',
                'color' => 'primary'
            ],

            "2" => [
                'text' => 'HPS',
                'color' => 'secondary'
            ],

            "3" => [
                'text' => 'Onsite Geox',
                'color' => 'success'
            ],

            "4" => [
                'text' => 'Part NBD',
                'color' => 'danger'
            ],

            "5" => [
                'text' => 'Others',
                'color' => 'warning'
            ],

            "6" => [
                'text' => 'Văn phòng phẩm/Tài liệu',
                'color' => 'info'
            ],



            default => [
                'text' => 'Unknown',
                'color' => 'primary'
            ]
        };
    }

    public function getShipmentTypeDataAttribute()
    {
        return match ($this->shipment_type){
             "1" => [
                'text' => 'Tài liệu',
                'color' => 'primary'
            ],

            "2" => [
                'text' => 'Thiết bị điện/điện tử',
                'color' => 'secondary'
            ],

            "3" => [
                'text' => 'Văn phòng phẩm',
                'color' => 'success'
            ],



            default => [
                'text' => 'Unknown',
                'color' => 'primary'
            ]
        };
    }
}
