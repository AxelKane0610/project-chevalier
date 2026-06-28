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
