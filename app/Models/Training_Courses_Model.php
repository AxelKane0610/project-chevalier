<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Training_Courses_Model extends Model
{
    //
    protected $table = 'training_courses';
    protected $fillable = [
        'training_no',
        'course_id',
        'course_name',
        'start_date',
        'end_date',
    ];

    
    
}
