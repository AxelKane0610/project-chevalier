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
        'type_of_ticket',
        'ticket_id',
        'id',
        'file_path',
        'name',
    ];

    public function attachments()
    {
        // Giả sử ticket_id là khóa ngoại ở bảng attachments_table
        return $this->hasMany(Attachments_Model::class, 'ticket_id', 'id');
    }

    
}
