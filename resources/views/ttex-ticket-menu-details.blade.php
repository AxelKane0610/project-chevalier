<!DOCTYPE html>
<html>
    <head>
        <title>Project Chevalier</title>
        <meta charset="utf-8">
        @vite([ 'resources/js/app.js', 'resources/js/ttex.js', 'resources/css/icons/themify-icons.css'])
        
    </head>

    <body>

        

        <x-common-header title="TTEX Ticket Details">
            <li>
                <a href="{{ url('/ttex-tickets-menu') }}" class="button">
                    <button><i class="ti-home"></i>
                    Home
                    </button>
                </a>
            </li>
            <li>
                <div class="search-container">
                    <form action="">
                        <button type="button" id="btn-toggle-search" class="nav-btn search-btn">
                            <i class="ti-search"></i> Search
                        </button>

                        <div id="search-dropdown" class="search-dropdown-box hidden">
                            <div class="search-input-group">
                                
                                @livewire('quick-search-dropdown')
                            </div>
                        </div>
                    </form>
                </div>
                
            </li>
            <li>
                <a href="{{ url('/main-menu') }}" class="button">
                    <button><i class="ti-layout-grid2"></i>
                    Quick Navigation
                    </button>
                </a>
            </li>

            @if( (auth()->user()->hasRole('ROLE_SUPER_ADMIN') || auth()->user()->hasRole('ROLE_TTEX_TICKET_ADMIN')) && $ticket->status == '1')
                
                <li>
                    <button type="button" class="js-input-required-btn" data-target="close-ttex-ticket"><i class="ti-check"></i> Close ticket</button>
                    
                </li>

            @endif
                
            </x-common-header>

        <div class="container-fluid px-4 py-4">
            <div class="row g-4" style="min-height: calc(100vh - 90px);">

                <!-- ================= Ticket Detail ================= -->
                <x-common-ticket-details-card 
                    :rows="[
                        [
                            'icon' => 'ti-receipt',
                            'label' => 'TTEX Bill',
                            'value' => $ticket->ttex_bill,
                            'type' => 'text'
                        ],
                        [
                            'icon' => 'ti-user',
                            'label' => 'Người request',
                            'value' => $ticket->user_owner->fullname ?? 'N/A',
                            'type' => 'text'
                        ],
                        [
                            'icon' => 'ti-list',
                            'label' => 'Category',
                            'value' => match ($ticket->category) {
                                '1' => 'ASRC',
                                '2' => 'HPS',
                                '3' => 'Onsite Geox',
                                '4' => 'Parts NBD',
                                '5' => 'Others',
                                '6' => 'Văn phòng phẩm/Tài liệu',
                            },
                            'type' => 'badge',
                            'color' => match ($ticket->category) {
                                '1' => 'primary',
                                '2' => 'secondary',
                                '3' => 'success',
                                '4' => 'danger',
                                '5' => 'warning',
                                '6' => 'info',
                                default => 'Unknown',
                            },
                        ],
                        [
                            'icon' => 'ti-list',
                            'label' => 'Shipment Type',
                            'value' => match ($ticket->shipment_type) {
                                '1' => 'Tài liệu',
                                '2' => 'Thiết bị điện/điện tử',
                                '3' => 'Văn phòng phẩm',
                                default => 'Unknown',
                            },
                            'type' => 'badge',
                            'color' => match ($ticket->shipment_type) {
                                '1' => 'primary',
                                '2' => 'secondary',
                                '3' => 'success',
                                default => 'Unknown',
                            },
                        ],
                        [
                            'icon' => 'ti-arrow-right',
                            'label' => 'Part Status',
                            'value' => match ($ticket->part_status) {
                                '1' => 'Good part',
                                '2' => 'Def part',
                                '3' => 'Good part/Unused',
                                default => 'Unknown',
                            },
                            'type' => 'badge',
                            'color' => 'primary'
                        ],
                        [
                            'icon' => 'ti-arrow-circle-right',
                            'label' => 'Status',
                            'value' => match ($ticket->status) {
                                '1' => 'Open - Chưa điều tin',
                                '2' => 'Completed - Đã điều tin',
                                '3' => 'Rejected',

                                default => 'Unknown',
                            },
                            'type' => 'badge',
                            'color' => match ($ticket->status) {
                                '1' => 'primary',
                                '2' => 'secondary',
                                '3' => 'danger',

                                default => 'Unknown',
                            },
                        ],
                        [
                            'icon' => 'ti-calendar',
                            'label' => 'Hạn trả def cho kho',
                            'value' => $ticket->part_return_deadline,
                            'type' => 'text'
                        ],
                        [
                            'icon' => 'ti-user',
                            'label' => 'Thông tin người gửi',
                            'value' => $ticket->sender_info,
                            'type' => 'text'
                        ],
                        [
                            'icon' => 'ti-user',
                            'label' => 'Thông tin người nhận',
                            'value' => $ticket->receiver_info,
                            'type' => 'text'
                        ],
                        [
                            'icon' => 'ti-align-justify',
                            'label' => 'Mô tả hàng hóa',
                            'value' => $ticket->shipment_description,
                            'type' => 'text'
                        ],
                        [
                            'icon' => 'ti-notepad',
                            'label' => 'Note',
                            'value' => $ticket->note,
                            'type' => 'text'
                        ],
                        [
                            'icon' => 'ti-calendar',
                            'label' => 'Ngày điều tin',
                            'value' => $ticket->booking_date,
                            'type' => 'text'
                        ],
                    ]"

                    
                >
                
                    <x-common-attachments-table-card
                        :attachments="$ticket->active_attachments"
                    />

                
                    <x-slot:footer>
                        @if(($ticket->user_id == auth()->user()->id) || auth()->user()->hasRole('ROLE_SUPER_ADMIN') || auth()->user()->hasRole('ROLE_TTEX_TICKET_ADMIN'))
                            <button type="button" class="js-input-required-btn" data-target="edit-ticket-details"><i class="ti-pencil"></i> Edit</button>
                        @endif
                    </x-slot:footer>
                

                </x-common-ticket-details-card>

                <!-- ================= Comment ================= -->

                <x-common-ticket-comments-card
                    :comments="$ticket->ticket_comments"
                    :showAttachments="true"
                    :actionRoute="route('add-comment-ttex-ticket', $ticket->id)"
                >


                </x-common-ticket-comments-card>

                <!-- ================= Timeline ================= -->

                <x-common-ticket-tracking-info
                    :trackings="$ticket->ticket_tracking_info"
                />
            </div>
        

        <x-common-ticket-form title="Edit TTEX Ticket" id="edit-ticket-details" action1="{{ route('edit-ttex-ticket', $ticket->id) }}">
            @method('PATCH')
            <label>TTEX Bill</label>
            <input type="text" class="ticket-form-body-input" name="ttex_bill" value="{{ $ticket->ttex_bill}}">

            <label>Category</label>
            <select name="category" class="ticket-form-body-input">
                <option value="1" @selected($ticket->category == '1')>ASRC</option>
                <option value="2" @selected($ticket->category == '2')>HPS</option>
                <option value="3" @selected($ticket->category == '3')>Onsite Geox</option>
                <option value="4" @selected($ticket->category == '4')>Part NBD</option>
                <option value="5" @selected($ticket->category == '5')>Others</option>
                <option value="6" @selected($ticket->category == '6')>Văn phòng phẩm/Tài liệu</option>

                
            </select>

            <label>Shipment Type</label>
            <select name="shipment_type" class="ticket-form-body-input">
                <option value="1" @selected($ticket->shipment_type == '1')>Tài liệu</option>
                <option value="2" @selected($ticket->shipment_type == '2')>Thiết bị điện/điện tử</option>
                <option value="3" @selected($ticket->shipment_type == '3')>Văn phòng phẩm</option>

                
            </select>

            <label>Part Status</label>
            <select name="part_status" class="ticket-form-body-input">
                <option value="1" @selected($ticket->part_status == '1')>Good part</option>
                <option value="2" @selected($ticket->part_status == '2')>Def part</option>
                <option value="3" @selected($ticket->part_status == '3')>Good part - Unused</option>

                
            </select>

            @if((auth()->user()->hasRole('ROLE_SUPER_ADMIN') || auth()->user()->hasRole('ROLE_TTEX_TICKET_ADMIN')) )
            <label>Status</label>
            <select name="status" class="ticket-form-body-input">
                <option value="1" @selected($ticket->status == '1')>Open - Chưa điều tin</option>
                <option value="2" @selected($ticket->status == '2')>Completed - Đã điều tin</option>
                <option value="3" @selected($ticket->status == '3')>Rejected</option>
                
            </select>
            @endif
            <label>Hạn trả def cho kho (Điền vào nếu đổi từ Good part sang Def hoặc Unused)</label>
            <input type="date" class="ticket-form-body-input" name="part_return_deadline" value="{{ $ticket->part_return_deadline}}">

            <label>Thông tin người gửi</label>
            <input type="text" class="ticket-form-body-input" name="sender_info" value="{{ $ticket->sender_info }}">

            <label>Thông tin người nhận</label>
            <input type="text" class="ticket-form-body-input" name="receiver_info" value="{{ $ticket->receiver_info }}">

            <label>Mô tả hàng hóa</label>
            <textarea class="ticket-form-body-input multiple-row" name="shipment_description">{{ $ticket->shipment_description }}</textarea>

            <label>Note</label>
            <input type="text" class="ticket-form-body-input" name="note" value="{{ $ticket->note }}">

            @if((auth()->user()->hasRole('ROLE_SUPER_ADMIN') || auth()->user()->hasRole('ROLE_TTEX_TICKET_ADMIN')) )
                <label>Ngày điều tin</label>
                <input type="date" class="ticket-form-body-input" name="booking_date" value="{{ $ticket->booking_date }}">
            @endif
            <label><b>Attachments</b></label>
            
            @if($ticket->active_attachments->count() > 0) 
                <x-common-attachments-table>
                    @foreach($ticket->active_attachments as $attachment)
                        <tr>
                            <td>
                                {{ $attachment->name ?? 'File đính kèm' }}
                            </td>
                            <td>

                                <div>
                                    
                                    <a href="{{ asset('attachments/' . $attachment->file_path) }}" target="_blank" class="btn btn-info">
                                        <i class="ti-eye"></i>
                                    </a>
                                    <input type="checkbox" name="delete_files[]" value="{{ $attachment->id }}" id="del_{{ $attachment->id }}">
                                    <label for="del_{{ $attachment->id }}">
                                        Xóa
                                    </label>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </x-common-attachments-table>
                        
                <small class="text-muted">Tích vào ô "Xóa" nếu muốn gỡ bỏ file đính kèm trước đó.</small>
            @else
                <p class="text-muted">Không có file nào được đính kèm</p>
            @endif
            
            <label class="ticket-form-body-input">Đính kèm thêm files:</label>
            <div class="upload-group ">
                <input class="ticket-form-body-input file-input" type="file" name="attachments[]" multiple>
                <ul class="file-list"></ul>
            </div>
            <x-slot:footer>
                <button class="ticket-form-body-input" type="submit" >Save</button> 
            </x-slot:footer>

        </x-common-ticket-form>

        <x-common-ticket-form title="Close TTEX Ticket" id="close-ttex-ticket" action1="{{ route('close-ttex-ticket', $ticket->id) }}">
            @method('PATCH')
            <label>Status</label>
            <select name="status" class="ticket-form-body-input" required>
                <option value="2">Completed - Đã điều tin</option>
                <option value="3">Rejected</option>
            </select>

            <label>Ngày điều tin (Default là ngày hiện tại)</label>
            <input type="date" class="ticket-form-body-input" name="booking_date" value="{{ now()->format('Y-m-d') }}">

            <label>Bill TTEX</label>
            <input type="text" class="ticket-form-body-input" name="ttex_bill" value="" placeholder="Điền vào nếu complete">

            <label>Comment</label>
            <input type="text" class="ticket-form-body-input" name="comment" value="" placeholder="Comment cho người tạo ticket nếu có">

            <x-slot:footer>
                <button class="ticket-form-body-input" type="submit" >Close ticket</button> 
            </x-slot:footer>

        </x-common-ticket-form>

        

    </body>

    <script>
  const textarea = document.getElementById('shipment_description');
  let isFocused = false;

  // Khi người dùng click vào ô
  textarea.addEventListener('focus', function() {
    if (!isFocused) {
      // Chỉ đưa con trỏ về đầu ở LẦN ĐẦU TIÊN focus vào ô
      setTimeout(() => {
        this.setSelectionRange(0, 0);
      }, 0); // setTimeout giúp chạy ngay sau khi trình duyệt định vị click mặc định
      isFocused = true;
    }
  });

  // Khi người dùng click ra ngoài (mất focus)
  textarea.addEventListener('blur', function() {
    // Reset lại trạng thái để lần sau click vào lại nhảy lên đầu
    isFocused = false;
  });
</script>

</html>