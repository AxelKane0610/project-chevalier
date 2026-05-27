<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Laser_Engraving_Tickets_Model extends Model
{
    //
    protected $table = 'laser_engraving_tickets';
    protected $fillable = [
        'user_id',
        'receipt',
        'status',
        'priority',
        'info_base',
        'barcode_info',
        'description',
    ];

    public function user_owner(): BelongsTo
    {
        // Một ticket thì "thuộc về" (belongsTo) một người dùng
        return $this->belongsTo(User::class, 'user_id'); //Bảo model sang model User để lấy thông tin user của ticket đó, dựa vào "user_id"
    }
}
