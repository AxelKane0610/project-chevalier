<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice_Exceptional_Tickets_Model extends Model
{
    //
    protected $table = 'invoice_exceptional_tickets';
    
    protected $fillable = [
        'user_id',
        'ticket_receipt',
        'invoice_number',
        'serial_number',
        'product_number',
        'expired_date',
        'invoice_date',
        'product_model',
        'description',
        'retail_name',
        'company_customer_name',
        'support_type',
        'status',
        'highest_approved_step'
    ];

    public function user_owner(): BelongsTo
    {
        // Một ticket thì "thuộc về" (belongsTo) một người dùng
        return $this->belongsTo(User::class, 'user_id'); //Bảo model sang model User để lấy thông tin user của ticket đó, dựa vào "user_id"
    }


    public function active_attachments()
    {
        return $this->hasMany(Attachments_Model::class, 'ticket_id', 'id')
            ->where(['type_of_ticket' => 7, 'status' => '1', 'comment_id' => null]); // Chỉ lấy những attachment có status = 1 (còn hiệu lực)
    }

    public function ticket_comments()
    {
        return $this->hasMany(Comments_Model::class, 'ticket_id', 'id') // Liên kết với model Comments_Model, dựa vào "ticket_id" để lấy những comment có ticket_id trùng với id của ticket này
            ->where(['type_of_ticket' => 7]); // Chỉ lấy những comment có type_of_ticket là 1 (software ticket)
        
    }

    public function ticket_tracking_info()
    {
        return $this->hasMany(tracking_info_model::class, 'ticket_id', 'id') // Liên kết với model tracking_info_model, dựa vào "ticket_id" để lấy những tracking có ticket_id trùng với id của ticket này
            ->where('type_of_ticket', 7);
    }

    public function getStatusDataAttribute()
    {
        return match ($this->status){
             "1" => [
                'text' => 'Open',
                'color' => 'primary'
            ],

            "2" => [
                'text' => 'Waiting approve invoice',
                'color' => 'secondary'
            ],

            "3" => [
                'text' => 'Waiting re-activate',
                'color' => 'info'
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

    public function getSupportTypeDataAttribute()
    {
        return match ($this->support_type){
             "1" => [
                'text' => 'Hóa đơn xuất sau (1 máy)',
                'color' => 'primary'
            ],

            "2" => [
                'text' => 'Hóa đơn xuất sau (Nhiều máy)',
                'color' => 'primary'
            ],

            "3" => [
                'text' => 'Kích hoạt bảo hành (1 máy)',
                'color' => 'primary'
            ],

            "4" => [
                'text' => 'Kích hoạt bảo hành (Nhiều máy)',
                'color' => 'primary'
            ],


            default => [
                'text' => 'Unknown',
                'color' => 'primary'
            ]
        };
    }

    
}
