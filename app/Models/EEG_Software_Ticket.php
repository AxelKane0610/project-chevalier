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

    // public function ticket_comments()
    // {
    //     return $this->hasMany(Comments_Model::class, 'ticket_id', 'id')
    //         ->where(['type_of_ticket' => 1]); // Chỉ lấy những comment có type_of_ticket là 1 (software ticket)
        
    // }


}
