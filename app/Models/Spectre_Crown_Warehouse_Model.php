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
        'note'
    ];

    public function active_attachments()
    {
        return $this->hasMany(Attachments_Model::class, 'ticket_id', 'id')
            ->where(['type_of_ticket' => 11, 'status' => '1']); // Chỉ lấy những attachment có status = 1 (còn hiệu lực)
    }

    public function ticket_comments()
    {
        return $this->hasMany(Comments_Model::class, 'ticket_id', 'id') // Liên kết với model Comments_Model, dựa vào "ticket_id" để lấy những comment có ticket_id trùng với id của ticket này
            ->where(['type_of_ticket' => 11]); // Chỉ lấy những comment có type_of_ticket là 1 (software ticket)
        
    }

    public function ticket_tracking_info()
    {
        return $this->hasMany(tracking_info_model::class, 'ticket_id', 'id') // Liên kết với model tracking_info_model, dựa vào "ticket_id" để lấy những tracking có ticket_id trùng với id của ticket này
            ->where('type_of_ticket', 11);
    }

    public function loan_unit_part_tickets()
    {
        return $this->hasMany(Loan_Unit_Part_Tickets_Model::class, 'loan_unit_asset_tag', 'asset_tag');
    }

    public function getWarehouseDataAttribute()
    {
        return match ($this->warehouse) {
            "1" => [
                'text' => 'SPECTRE',
                'class' => 'spectre'
            ],

            "2" => [
                'text' => 'CROWN HCM',
                'class' => 'crown-hcm'
            ],

            "3" => [
                'text' => 'CROWN HN',
                'class' => 'crown-hn'
            ],


            default => [
                'text' => 'Unknown',
                'class' => 'unknown'
            ]
        };
    }

    public function getAvailableStatusDataAttribute()
    {
        return match ($this->available_status) {
            "1" => [
                'text' => 'Available',
                'class' => 'available'
            ],

            "2" => [
                'text' => 'Not Available',
                'class' => 'not-available'
            ],

            "3" => [
                'text' => 'In use',
                'class' => 'in-use'
            ],


            default => [
                'text' => 'Unknown',
                'class' => 'unknown'
            ]
        };
    }

    public function getConditionDataAttribute()
    {
        return match ($this->condition) {
            "1" => [
                'text' => 'Good working',
                'class' => 'good-working'
            ],

            "2" => [
                'text' => 'Chưa test',
                'class' => 'not-tested'
            ],

            "3" => [
                'text' => 'Can\'t use',
                'class' => 'can-t-use'
            ],

            default => [
                'text' => 'Unknown',
                'class' => 'unknown'
            ]
        };
    }

    public function getCategoryDataAttribute()
    {
        return match ($this->category) {
            "1" => [
                'text' => 'Laptop',
                'class' => 'laptop'
            ],

            "2" => [
                'text' => 'Accessories (Chuột, phím,...)',
                'class' => 'accessories'
            ],

            "3" => [
                'text' => 'Màn hình',
                'class' => 'monitor'
            ],

            "4" => [
                'text' => 'Máy scanner',
                'class' => 'scanner'
            ],

            "5" => [
                'text' => 'PC',
                'class' => 'pc'
            ],

            "6" => [
                'text' => 'Máy in khổ lớn',
                'class' => 'large-format-printer'
            ],

            "7" => [
                'text' => 'Máy in khổ nhỏ',
                'class' => 'small-format-printer'
            ],

            "8" => [
                'text' => 'Others',
                'class' => 'others'
            ],


            default => [
                'text' => 'Unknown',
                'class' => 'unknown'
            ]
        };
    }




}
