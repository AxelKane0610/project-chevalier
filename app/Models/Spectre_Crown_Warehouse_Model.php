<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Spectre_Crown_Warehouse_Model extends Model
{
    //
    protected $table = 'spectre_crown_warehouse';
    protected $fillable = [
        'asset_tag',
        'serial_number',
        'box_serial_number',
        'product_number',
        'model',
        'category',
        'asset_type',
        'quantity',
        'warehouse',
        'available_status',
        'condition',
        'note',
        'import_date',
    ];

    public function active_attachments()
    {
        return $this->hasMany(Attachments_Model::class, 'ticket_id', 'id')
            ->where(['type_of_ticket' => 11, 'status' => '1', 'comment_id' => null]); // Chỉ lấy những attachment có status = 1 (còn hiệu lực)
    }

    public function ticket_comments()
    {
        return $this->hasMany(Comments_Model::class, 'ticket_id', 'id')
            ->where(['type_of_ticket' => 11]); 
        
    }

    public function ticket_tracking_info()
    {
        return $this->hasMany(tracking_info_model::class, 'ticket_id', 'id') // Liên kết với model tracking_info_model, dựa vào "ticket_id" để lấy những tracking có ticket_id trùng với id của ticket này
            ->where('type_of_ticket', 11);
    }

    public function loan_unit_part_tickets()
    {
        return $this->hasMany(Loan_Unit_Ticket_Parts_Details_Model::class, 'loan_unit_asset_tag', 'asset_tag');
    }

    public function getWarehouseDataAttribute()
    {
        return match ($this->warehouse) {
            "1" => [
                'text' => 'SPECTRE',
                'color' => 'primary'
            ],

            "2" => [
                'text' => 'CROWN HCM',
                'color' => 'secondary'
            ],

            "3" => [
                'text' => 'CROWN HN',
                'color' => 'warning'
            ],


            default => [
                'text' => 'Unknown',
                'color' => 'unknown'
            ]
        };
    }

    public function getAvailableStatusDataAttribute()
    {
        return match ($this->available_status) {
            "1" => [
                'text' => 'Available',
                'color' => 'success'
            ],

            "2" => [
                'text' => 'Not Available',
                'color' => 'secondary'
            ],

            "3" => [
                'text' => 'In use',
                'color' => 'warning'
            ],


            default => [
                'text' => 'Unknown',
                'color' => 'primary'
            ]
        };
    }

    public function getConditionDataAttribute()
    {
        return match ($this->condition) {
            "1" => [
                'text' => 'Good working',
                'color' => 'primary'
            ],

            "2" => [
                'text' => 'Chưa test',
                'color' => 'secondary'
            ],

            "3" => [
                'text' => 'Can\'t use',
                'color' => 'danger'
            ],

            default => [
                'text' => 'Unknown',
                'color' => 'primary'
            ]
        };
    }

    public function getCategoryDataAttribute()
    {
        return match ($this->category) {
            "1" => [
                'text' => 'Laptop',
                'color' => 'primary'
            ],

            "2" => [
                'text' => 'Accessories (Chuột, phím,...)',
                'color' => 'secondary'
            ],

            "3" => [
                'text' => 'Màn hình',
                'color' => 'warning'
            ],

            "4" => [
                'text' => 'Máy scanner',
                'color' => 'success'
            ],

            "5" => [
                'text' => 'PC',
                'color' => 'danger'
            ],

            "6" => [
                'text' => 'Máy in khổ lớn',
                'color' => 'dark'
            ],

            "7" => [
                'text' => 'Máy in khổ nhỏ',
                'color' => 'info'
            ],

            "8" => [
                'text' => 'Others',
                'color' => 'info'
            ],


            default => [
                'text' => 'Unknown',
                'color' => 'primary'
            ]
        };
    }




}
