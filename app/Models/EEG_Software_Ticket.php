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

    public function attachments()
    {
        // Giả sử ticket_id là khóa ngoại ở bảng attachments_table
        return $this->hasMany(Attachments_Model::class, 'ticket_id', 'id') // Lấy tất cả attachment có ticket_id trùng với id của ticket này
            ->where('type_of_ticket', 1); // Chỉ lấy attachment có type_of_ticket là 1 (software ticket)
    }
}
