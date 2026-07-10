<?php

use Livewire\Component;
use App\Models\Spectre_Crown_Warehouse_Model; // Hãy nhớ đổi tên Model này thành bảng dữ liệu của bạn nhé

new class extends Component
{
    public $search = '';         // Lưu chuỗi người dùng gõ
    public $results = [];        // Danh sách mảng chứa các kết quả tìm được từ DB
    public $showDropdown = false; // Trạng thái ẩn/hiện của dropdown
    public $selectedId = null;   // Lưu ID của dòng được chọn sau cùng

    // Hàm này tự động chạy khi người dùng gõ chữ vào ô input
    public function updatedSearch()
    {
        if (strlen($this->search) >= 1) {
            // Tìm kiếm trong DB theo Tên, Số điện thoại hoặc Địa chỉ
            $this->results = Spectre_Crown_Warehouse_Model::where('serial_number', 'like', '%' . $this->search . '%')
                ->orWhere('asset_tag', 'like', '%' . $this->search . '%')
                ->orWhere('product_number', 'like', '%' . $this->search . '%')
                ->limit(8) // Giới hạn 8 dòng cho mượt
                ->get();

            $this->showDropdown = true;
        } else {
            $this->results = [];
            $this->showDropdown = false;
        }
    }

    // Hàm xử lý khi click chọn 1 dòng
    public function selectItem($id, $displayText)
    {
        $this->selectedId = $id;       
        $this->search = $displayText;  
        $this->showDropdown = false;   
    }

    // Hàm mở lại dropdown khi click vào input
    public function openDropdown()
    {
        if (strlen($this->search) >= 1) {
            $this->showDropdown = true;
        }
    }
};
?>

<div class="dropdown-container" style="position: relative; width: 100%; max-width: 700px; font-family: Arial, sans-serif;">
    
    <input 
        type="text" 
        wire:model.live="search"
        wire:click="openDropdown"
        placeholder="Nhập tên, địa chỉ hoặc số điện thoại để tìm kiếm..."
        style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; font-size: 15px;"
        autocomplete="off"
    />

    <input type="hidden" name="serial_number" value="{{ $selectedId }}">

    @if($showDropdown && count($results) > 0)
        <ul style="position: absolute; top: 100%; left: 0; width: 100%; background: white; border: 1px solid #ccc; list-style: none; margin: 0; padding: 0; max-height: 350px; overflow-y: auto; z-index: 9999; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
            
            @foreach($results as $item)
                @php
                    $displayText = "{$item->serial_number} - {$item->asset_tag} - {$item->model}";
                @endphp
                
                <li 
                    wire:click="selectItem({{ $item->id }}, '{{ $displayText }}')"
                    class="{{ $selectedId == $item->id ? 'active-item' : '' }}"
                    style="padding: 12px; cursor: pointer; border-bottom: 1px solid #eee; font-size: 14px; transition: background 0.2s;"
                    onmouseover="this.style.backgroundColor='#f5f5f5'"
                    onmouseout="this.style.backgroundColor=''"
                >
                    {{ $displayText }}
                </li>
            @endforeach

        </ul>
    @endif

    <style>
        .active-item {
            background-color: #e60023 !important;
            color: white !important;
        }
    </style>
</div>