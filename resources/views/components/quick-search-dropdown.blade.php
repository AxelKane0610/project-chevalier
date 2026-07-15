

<?php

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

new class extends Component
{
    public $search = '';         // Lưu chuỗi người dùng gõ
    public $results = [];        // Danh sách kết quả tìm được
    public $showDropdown = false; // Trạng thái ẩn/hiện của dropdown
    public $selectedId = null;   // Lưu ID (hoặc chuỗi định danh) của dòng được chọn

    public function updatedSearch()
    {
        if (strlen($this->search) >= 1) {
            
            // 1. Dùng SQL thuần để lấy tất cả các bảng trong Database MySQL
            $tables = DB::select('SHOW TABLES');
            $query = null;

            // Tên key trả về của 'SHOW TABLES' sẽ có dạng 'Tables_in_ten_database_cua_ban'
            // Chúng ta dùng current((array)$table) để lấy giá trị text đầu tiên một cách an toàn
            foreach ($tables as $table) {
                $tableName = current((array)$table);

                // Bỏ qua các bảng hệ thống
                if (in_array($tableName, ['migrations', 'failed_jobs', 'password_reset_tokens', 'personal_access_tokens', 'sessions','attachments_table'])) {
                    continue;
                }

                if (Schema::hasColumn($tableName, 'ticket_receipt')) {
                    
                    // 1. Định nghĩa tiền tố link (URL Prefix) cho từng bảng cụ thể
                    // Bạn hãy đổi 'table_a', 'table_b' thành tên bảng thực tế trong DB của bạn
                    switch ($tableName) {
                        case 'eeg_software_tickets':
                            $ticketTypeName = 'EEG Software Support';
                            $urlPrefix = '/software-tickets-menu-details/';
                            break;
                        case 'ttex_tickets':
                            $urlPrefix = '/ttex-tickets-menu-details/';
                            $ticketTypeName = 'Điều tin TTEX';
                            break;
                        case 'laser_engraving_tickets':
                            $urlPrefix = '/laser-engraving-menu-details/';
                            $ticketTypeName = 'Khắc base';
                            break;
                        case 'loan_unit_part_tickets':
                            $urlPrefix = '/loan-unit-part-ticket-details/';
                            $ticketTypeName = 'Mượn máy & part';
                            break;
                        case 'invoice_exceptional_tickets':
                            $urlPrefix = '/invoice-exceptional-menu-details/';
                            $ticketTypeName = 'Invoice Exceptional';
                            break;
                        case 'thermal_event_exceptional_tickets':
                            $urlPrefix = '/thermal-event-tickets-menu-details/';
                            $ticketTypeName = 'Thermal Event';
                            break;
                        
                    }
                    
                    // 2. Tạo subQuery và dùng CONCAT để nối tiền tố link với ID của dòng đó
                    $subQuery = DB::table($tableName)
                        ->select(
                            DB::raw("'{$tableName}' as table_source"), 
                            'id', 
                            'ticket_receipt as main_field',
                            DB::raw("CONCAT('{$ticketTypeName}', ' - Receipt: ', ticket_receipt) as description"),
                            // Tự động tạo link: ví dụ /tickets/table-a/15
                            DB::raw("CONCAT('{$urlPrefix}', id) as ticket_url") 
                        )
                        ->where('ticket_receipt', 'like', '%' . $this->search . '%');

                    if ($query === null) {
                        $query = $subQuery;
                    } else {
                        $query->unionAll($subQuery);
                    }
                }
            }

            // 2. Thực thi câu truy vấn tổng hợp
            if ($query) {
                $this->results = DB::table(DB::raw("({$query->toSql()}) as search_results"))
                    ->mergeBindings($query) 
                    ->limit(8)
                    ->get()
                    ->toArray();
            } else {
                $this->results = [];
            }

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
        placeholder="Nhập số phiếu để tìm kiếm nhanh..."
        style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; font-size: 15px;"
        autocomplete="off"
    />

    <input type="hidden" name="selected_id" value="{{ $selectedId }}">

    @if($showDropdown && count($results) > 0)
        <ul style="
            position: absolute; 
            top: 100%; 
            left: 0; 
            width: 100%; 
            background: white; 
            border: 1px solid #ccc; 
            list-style: none; 
            margin: 0; padding: 0; 
            max-height: 350px; 
            overflow-y: 
            auto; 
            z-index: 9999; 
            box-shadow: 0 4px 8px rgba(0,0,0,0.1); 
            display: flex; flex-direction: column;">
            
            @foreach($results as $item)
                @php
                    $displayText = $item->description; 
                @endphp
                
                <li 
                    class="{{ $selectedId == ($item->table_source . '_' . $item->id) ? 'active-item' : '' }}"
                    style="border-bottom: 1px solid #eee; font-size: 14px; transition: background 0.2s; padding: 0;"
                    onmouseover="this.style.backgroundColor='#f5f5f5'"
                    onmouseout="this.style.backgroundColor=''"
                >
                    <a 
                        href="{{ $item->ticket_url }}" 
                        wire:navigate
                        style="display: block; padding: 12px; text-decoration: none; color: inherit;"
                    >
                        {{ $displayText }}
                    </a>
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