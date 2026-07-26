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
        'ticket_receipt',
        'support_type',
        'priority',
        'description',
        'ticket_completed_by',
        'completed_date'
    ];

    public function user_owner(): BelongsTo
    {
        // Một ticket thì "thuộc về" (belongsTo) một người dùng
        return $this->belongsTo(User::class, 'user_id'); //Bảo model sang model User để lấy thông tin user của ticket đó, dựa vào "user_id"
    }


    public function active_attachments()
    {
        return $this->hasMany(Attachments_Model::class, 'ticket_id', 'id')
            ->where(['type_of_ticket' => 1, 'status' => '1','comment_id' => null]); // Chỉ lấy những attachment có status = 1 (còn hiệu lực)
    }

    public function ticket_comments()
    {
        return $this->hasMany(Comments_Model::class, 'ticket_id', 'id') // Liên kết với model Comments_Model, dựa vào "ticket_id" để lấy những comment có ticket_id trùng với id của ticket này
            ->where(['type_of_ticket' => 1]); // Chỉ lấy những comment có type_of_ticket là 1 (software ticket)
        
    }

    public function ticket_tracking_info()
    {
        return $this->hasMany(tracking_info_model::class, 'ticket_id', 'id') // Liên kết với model tracking_info_model, dựa vào "ticket_id" để lấy những tracking có ticket_id trùng với id của ticket này
            ->where('type_of_ticket', 1);
    }

    public function completed_by(): BelongsTo
    {
        // Một ticket thì "thuộc về" (belongsTo) một người dùng
        return $this->belongsTo(User::class, 'ticket_completed_by'); 
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
                'text' => 'Open',
                'color' => 'primary'
            ],

            "2" => [
                'text' => 'In Progress',
                'color' => 'secondary'
            ],

            "3" => [
                'text' => 'Waiting Approval',
                'color' => 'info'
            ],

            "4" => [
                'text' => 'Completed',
                'color' => 'success'
            ],

            "5" => [
                'text' => 'Rejected',
                'color' => 'light'
            ],

            "6" => [
                'text' => 'Cancel',
                'color' => 'dark'
            ],


            default => [
                'text' => 'Unknown',
                'color' => 'primary'
            ]
        };
    }

    public function getSupportTypeDataAttribute(){
        return match ($this->support_type){
            "1" => [
                'text' => 'Thêm mã part/product',
                'color' => 'primary'
            ],

            "2" => [
                'text' => 'Rollback',
                'color' => 'primary'
            ],

            "3" => [
                'text' => 'Hủy số phiếu/Ẩn lịch sử bảo hành',
                'color' => 'primary'
            ],

            "4" => [
                'text' => 'Điều chỉnh thông tin',
                'color' => 'primary'
            ],

            "5" => [
                'text' => 'Unmark Re-Repair',
                'color' => 'primary'
            ],

            "6" => [
                'text' => 'Lỗi hệ thống',
                'color' => 'primary'
            ],

            "7" => [
                'text' => 'Cấp quyền export data',
                'color' => 'primary'
            ],

            "8" => [
                'text' => 'Đề xuất thay đổi/cải tiến',
                'color' => 'primary'
            ],

            "9" => [
                'text' => 'Vấn đề khác',
                'color' => 'primary'
            ],


            default => [
                'text' => 'Unknown',
                'color' => 'primary'
            ]
        };
    }

}
