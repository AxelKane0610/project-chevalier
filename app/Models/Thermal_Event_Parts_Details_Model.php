<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Thermal_Event_Parts_Details_Model extends Model
{
    //
    protected $table = 'thermal_events_parts_details';
    protected $fillable = [
        'ticket_id',
        'part_mo_number',
        'part_number',
        'part_description',
        'part_ct_number',
        'status',
    ];

    public function ticket_owner(): BelongsTo
    {
        return $this->belongsTo(Thermal_Event_Exceptional_Tickets_Model::class, 'ticket_id', 'id'); //Bảo model sang model Thermal_Event_Exceptional_Tickets_Model để lấy thông tin ticket của part details đó, dựa vào "ticket_id"
    }
}
