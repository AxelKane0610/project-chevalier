<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HPS_Warehouse_Model extends Model
{
    //
    protected $table = 'hps_warehouse';
    protected $fillable = [
        'asset_tag',
        'user_id',
        'serial_number',
        'box_serial_number',
        'product_number',
        'model',
        'import_date',
        'import_source',
        'po_number',
        'po_type',
        'invoice',
        'price',
        'import_status',
        'site',
        'location',
        'note',
        'current_status',
        'current_hps_receipt',
        'current_serial_number',
        'current_box_serial_number',
        'current_product_number',
        'current_site',
        'current_location',
        'unit_re_import_status',
        'current_export_ticket_id',
    ];

    public function active_attachments()
    {
        return $this->hasMany(Attachments_Model::class, 'ticket_id', 'id')
            ->where(['type_of_ticket' => 12, 'status' => '1', 'comment_id' => null]); // Chỉ lấy những attachment có status = 1 (còn hiệu lực)
    }

    public function ticket_comments()
    {
        return $this->hasMany(Comments_Model::class, 'ticket_id', 'id')
            ->where(['type_of_ticket' => 12]); 
        
    }

    public function ticket_tracking_info()
    {
        return $this->hasMany(tracking_info_model::class, 'ticket_id', 'id') // Liên kết với model tracking_info_model, dựa vào "ticket_id" để lấy những tracking có ticket_id trùng với id của ticket này
            ->where('type_of_ticket', 12);
    }
}
