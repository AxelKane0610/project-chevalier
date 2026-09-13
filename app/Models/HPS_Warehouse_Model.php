<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function user_owner(): BelongsTo
    {
        // Một ticket thì "thuộc về" (belongsTo) một người dùng
        return $this->belongsTo(User::class, 'user_id'); //Bảo model sang model User để lấy thông tin user của ticket đó, dựa vào "user_id"
    }

    public function export_details()
    {
        return $this->hasMany(HPS_Warehouse_Export_Details_Model::class, 'asset_tag', 'asset_tag');
    }

    public function getImportSourceDataAttribute()
    {
        return match ($this->import_source) {
            "1" => [
                'text' => 'FPT',
                'color' => 'primary'
            ],

            "2" => [
                'text' => 'DGW',
                'color' => 'secondary'
            ],

            "3" => [
                'text' => 'SRFR',
                'color' => 'success'
            ],


            default => [
                'text' => 'Unknown',
                'color' => 'primary'
            ]
        };
    }

    public function getPOTypeDataAttribute()
    {
        return match ($this->po_type) {
            "1" => [
                'text' => 'E-Claim',
                'color' => 'primary'
            ],

            "2" => [
                'text' => 'PO Remaining',
                'color' => 'secondary'
            ],

            "3" => [
                'text' => 'Forecast',
                'color' => 'success'
            ],


            default => [
                'text' => 'Unknown',
                'color' => 'primary'
            ]
        };
    }

    public function getImportStatusDataAttribute()
    {
        return match ($this->import_status) {
            "1" => [
                'text' => 'New FB',
                'color' => 'primary'
            ],

            "2" => [
                'text' => 'RFB',
                'color' => 'secondary'
            ],


            default => [
                'text' => 'Unknown',
                'color' => 'primary'
            ]
        };
    }

    public function getSiteDataAttribute()
    {
        return match ($this->site) {
            "1" => [
                'text' => 'HCM',
                'color' => 'primary'
            ],

            "2" => [
                'text' => 'HN',
                'color' => 'secondary'
            ],

            "3" => [
                'text' => 'DN',
                'color' => 'success'
            ],

            "4" => [
                'text' => 'CT',
                'color' => 'danger'
            ],


            default => [
                'text' => 'Unknown',
                'color' => 'primary'
            ]
        };
    }

    public function getCurrentStatusDataAttribute()
    {
        return match ($this->current_status) {
            "1" => [
                'text' => 'Đang lưu kho',
                'color' => 'success'
            ],

            "2" => [
                'text' => 'Đã hủy',
                'color' => 'danger'
            ],

            "3" => [
                'text' => 'Đã xuất máy, chờ thu hồi',
                'color' => 'primary'
            ],

            "4" => [
                'text' => 'Chờ hủy',
                'color' => 'warning'
            ],

            "5" => [
                'text' => 'Handover',
                'color' => 'secondary'
            ],


            default => [
                'text' => 'Unknown',
                'color' => 'primary'
            ]
        };
    }

    public function getCurrentSiteDataAttribute()
    {
        return match ($this->current_site) {
            "1" => [
                'text' => 'HCM',
                'color' => 'primary'
            ],

            "2" => [
                'text' => 'HN',
                'color' => 'secondary'
            ],

            "3" => [
                'text' => 'DN',
                'color' => 'success'
            ],

            "4" => [
                'text' => 'CT',
                'color' => 'danger'
            ],


            default => [
                'text' => 'Unknown',
                'color' => 'primary'
            ]
        };
    }

    public function getUnitReImportStatusDataAttribute()
    {
        return match ($this->unit_re_import_status) {
            "1" => [
                'text' => 'New',
                'color' => 'primary'
            ],

            "2" => [
                'text' => 'Good',
                'color' => 'secondary'
            ],

            "3" => [
                'text' => 'DOA',
                'color' => 'success'
            ],

            "4" => [
                'text' => 'Not good',
                'color' => 'danger'
            ],

            "5" => [
                'text' => 'Scrap',
                'color' => 'warning'
            ],


            default => [
                'text' => 'Unknown',
                'color' => 'primary'
            ]
        };
    }
}
