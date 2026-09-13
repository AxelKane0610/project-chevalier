<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HPS_Warehouse_Export_Details_Model extends Model
{
    //
    protected $table = 'hps_export_details';
    protected $fillable = [
        'ce_owner',
        'asset_tag',
        'export_date',
        'export_site',
        'export_location',
        'export_product_number',
        'export_model',
        'export_serial_number',
        'export_box_serial_number',
        'hps_receipt',
        'program_support',
        're_import_model',
        're_import_serial_number',
        're_import_box_serial_number',
        're_import_product_number',
        're_import_site',
        're_import_location',
        'current_status',
        'note',
        're_import_status',
        'user_export',
        'user_re_import',
        're_import_date'
    ];

    public function item_owner()
    {
        return $this->belongsTo(HPS_Warehouse_Model::class, 'asset_tag', 'asset_tag');
    }

    public function ceOwner(): BelongsTo
    {
        // Một ticket thì "thuộc về" (belongsTo) một người dùng
        return $this->belongsTo(User::class, 'ce_owner', 'id'); //Bảo model sang model User để lấy thông tin user của ticket đó, dựa vào "user_id"
    }

    public function user_owner_export(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_export', 'id'    ); //Bảo model sang model User để lấy thông tin user của ticket đó, dựa vào "user_id"
    }

    public function user_owner_re_import(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_re_import', 'id'); //Bảo model sang model User để lấy thông tin user của ticket đó, dựa vào "user_id"
    }
}
