<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EEG_Software_Ticket extends Model
{
    //
    use HasFactory;
    protected $table = 'eeg_software_tickets';
    
    protected $fillable = [
        'user_id',
        'ticket_reciept',
        'support_type',
        'priority',
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
            ->where(['type_of_ticket' => 1, 'status' => '1']); // Chỉ lấy những attachment có status = 1 (còn hiệu lực)
    }

    public function ticket_comments()
    {
        return $this->hasMany(Comments_Model::class, 'ticket_id', 'id')
            ->where(['type_of_ticket' => 1]); // Chỉ lấy những comment có type_of_ticket là 1 (software ticket)
        
    }

    public function getPriorityDataAttribute()
    {
        return match($this->priority){
            "1" => [
                'text' => 'Normal',
                'class' => 'normal'
            ],

            "2" => [
                'text' => 'Critical',
                'class' => 'critical'
            ],

            "3" => [
                'text' => 'High',
                'class' => 'high'
            ],

            "4" => [
                'text' => 'Low',
                'class' => 'low'
            ],

            default => [
                'text' => 'Unknown',
                'class' => 'unknown'
            ]
        };
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
                'text' => 'Waiting Approval',
                'class' => 'waiting-approval'
            ],

            "4" => [
                'text' => 'Complete',
                'class' => 'complete'
            ],

            "5" => [
                'text' => 'Rejected',
                'class' => 'rejected'
            ],

            "6" => [
                'text' => 'Cancel',
                'class' => 'cancel'
            ],


            default => [
                'text' => 'Unknown',
                'class' => 'unknown'
            ]
        };
    }

    public function getSupportTypeDataAttribute(){
        return match ($this->support_type){
             "1" => [
                'text' => 'Thêm mã part/product',
                'class' => 'add-part-product'
            ],

            "2" => [
                'text' => 'Rollback',
                'class' => 'rollback'
            ],

            "3" => [
                'text' => 'Hủy số phiếu/Ẩn lịch sử bảo hành',
                'class' => 'cancel-reciept'
            ],

            "4" => [
                'text' => 'Điều chỉnh thông tin',
                'class' => 'adjust-information'
            ],

            "5" => [
                'text' => 'Unmark Re-Repair',
                'class' => 'unmark-rerepair'
            ],

            "6" => [
                'text' => 'Lỗi hệ thống',
                'class' => 'system-issue'
            ],

            "7" => [
                'text' => 'Cấp quyền export data',
                'class' => 'data-export-request'
            ],

            "8" => [
                'text' => 'Đề xuất thay đổi/cải tiến',
                'class' => 'request-improvement'
            ],

            "9" => [
                'text' => 'Vấn đề khác',
                'class' => 'others'
            ],


            default => [
                'text' => 'Unknown',
                'class' => 'unknown'
            ]
        };
    }

}
