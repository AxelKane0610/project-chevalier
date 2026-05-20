<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comments_Model extends Model
{
    //
    use HasFactory;
    protected $table = 'comments_table';

    protected $fillable = [
        'id',
        'ticket_id',
        'type_of_ticket',
        'user_id',
        'comment',
    ];

    public function attachments()
    {
        return $this->hasMany(Attachments_Model::class, 'comment_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
