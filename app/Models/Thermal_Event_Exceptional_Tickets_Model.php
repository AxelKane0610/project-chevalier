<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Thermal_Event_Exceptional_Tickets_Model extends Model
{
    //
    protected $table = 'thermal_event_exceptional_tickets';
    protected $fillable = [
        'user_id',
        'ticket_receipt',
        'status',
        'serial_number',
        'product_number',
        'product_model',
        'description',
        'cdax_id',
        'customer_type',
        'company_customer_name',
        'user_observations',
    ];

    public function user_owner(): BelongsTo
    {
        // Một ticket thì "thuộc về" (belongsTo) một người dùng
        return $this->belongsTo(User::class, 'user_id'); //Bảo model sang model User để lấy thông tin user của ticket đó, dựa vào "user_id"
    }

    public function active_attachments()
    {
        return $this->hasMany(Attachments_Model::class, 'ticket_id', 'id')
            ->where(['type_of_ticket' => 10, 'status' => '1', 'comment_id' => null]); // Chỉ lấy những attachment có status = 1 (còn hiệu lực)
    }

    public function ticket_comments()
    {
        return $this->hasMany(Comments_Model::class, 'ticket_id', 'id') // Liên kết với model Comments_Model, dựa vào "ticket_id" để lấy những comment có ticket_id trùng với id của ticket này
            ->where(['type_of_ticket' => 10]); // Chỉ lấy những comment có type_of_ticket là 10 (thermal event exceptional ticket)
        
    }

    public function ticket_tracking_info()
    {
        return $this->hasMany(tracking_info_model::class, 'ticket_id', 'id') // Liên kết với model tracking_info_model, dựa vào "ticket_id" để lấy những tracking có ticket_id trùng với id của ticket này
            ->where('type_of_ticket', 10);
    }

    public function parts_details()
    {
        return $this->hasMany(Thermal_Event_Parts_Details_Model::class,'ticket_id','id')
            ->where('status', '1'); // Liên kết với model Thermal_Event_Parts_Details_Model, dựa vào "ticket_id" để lấy những part details có ticket_id trùng với id của ticket này
    }

    public function getStatusDataAttribute()
    {
        return match ($this->status){
             "1" => [
                'text' => 'Open',
                'color' => 'primary'
            ],

            "2" => [
                'text' => 'Waiting for verifier',
                'color' => 'secondary'
            ],

            "3" => [
                'text' => 'Waiting for approver',
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

    public function getCustomerTypeDataAttribute()
    {
        return match ($this->customer_type){
             "1" => [
                'text' => 'Khách hàng lẻ',
                'color' => 'primary'
            ],

            "2" => [
                'text' => 'Khách hàng công ty/doanh nghiệp',
                'color' => 'danger'
            ],

            "3" => [
                'text' => 'T1/Đại lý bán lẻ',
                'color' => 'secondary'
            ],


            default => [
                'text' => 'Unknown',
                'color' => 'primary'
            ]
        };
    }
}
