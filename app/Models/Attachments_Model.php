<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attachments_Model extends Model
{
    //
    use HasFactory;
    protected $table = 'attachments_table';

    protected $fillable = [
        'user_id',
        'type_of_ticket',
        'ticket_id',
        'id',
        'file_path',
        'name',
        'comment_id',
    ];


    public function comment()
    {
        return $this->belongsTo(Comments_Model::class);
    }

    public function user_owner(): BelongsTo
    {
        // Một ticket thì "thuộc về" (belongsTo) một người dùng
        return $this->belongsTo(User::class, 'user_id'); //Bảo model sang model User để lấy thông tin user của ticket đó, dựa vào "user_id"
    }

    
}
