<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EEG_Software_Ticket;
use App\Models\Out_Of_Office_Tickets_Model;
use App\Models\Invoice_Exceptional_Tickets_Model;

use App\Models\Comments_Model;
use App\Models\HPS_Warehouse_Export_Details_Model;
use App\Models\HPS_Warehouse_Model;
use App\Models\Scrap_Request_Model;
use App\Models\Thermal_Event_Exceptional_Tickets_Model;
use App\Services\tracking_info_service;


class ApprovalController extends Controller
{
    //
    public function handleCallback(Request $request)
    {
        // 1. Log dữ liệu để kiểm tra (tùy chọn)
        

        // 2. Lấy dữ liệu từ Power Automate gửi sang
        $approval_response['ticket_id'] = $request->input('ticket_id');
        $approval_response['type_of_ticket'] = $request->input('type_of_ticket');
        $approval_response['outcome'] = $request->input('outcome'); // Approve hoặc Reject
        $approval_response['approver_comment'] = $request->input('approver_comment');

        

        
        
        if ($approval_response['outcome'] === 'Approve' && $approval_response['type_of_ticket'] === '1') 
        {
            $ticket = EEG_Software_Ticket::find($approval_response['ticket_id']);
            if ($ticket->status == '3') {
                tracking_info_service::add(
                    $ticket->id,
                    10,
                    1,
                    'received approved response from Power Automate',
                );
                Comments_Model::create([
                    'ticket_id' => $approval_response['ticket_id'],
                    'type_of_ticket' => $approval_response['type_of_ticket'],
                    'user_id' => 10,
                    'comment'=> $approval_response['approver_comment']

                ]);
                $ticket->status = '2'; 
                $ticket->save();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Trạng thái ticket đã được cập nhật'
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Ticket không ở trạng thái chờ phê duyệt'
                ], 200);
            }
        }

        if ($approval_response['outcome'] === 'Reject' && $approval_response['type_of_ticket'] === '1') 
        {
            $ticket = EEG_Software_Ticket::find($approval_response['ticket_id']);
            if ($ticket->status == '3') {
                tracking_info_service::add(
                    $ticket->id,
                    10,
                    1,
                    'received rejected response from Power Automate',
                );
                Comments_Model::create([
                    'ticket_id' => $approval_response['ticket_id'],
                    'type_of_ticket' => $approval_response['type_of_ticket'],
                    'user_id' => 10,
                    'comment'=> $approval_response['approver_comment']

                ]);
                $ticket->status = '2'; 
                $ticket->save();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Trạng thái ticket đã được cập nhật'
                ], 200);

            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Ticket không ở trạng thái chờ phê duyệt'
                ], 200);
            }
        } 

        if ($approval_response['outcome'] === 'Approve' && $approval_response['type_of_ticket'] === '7') 
        {
            $ticket = Invoice_Exceptional_Tickets_Model::find($approval_response['ticket_id']);
            if ($ticket->status == '2' && ($ticket->support_type == '1' || $ticket->support_type == '2')) {
                tracking_info_service::add(
                    $ticket->id,
                    10,
                    7,
                    'received approved response from Power Automate',
                );
                Comments_Model::create([
                    'ticket_id' => $approval_response['ticket_id'],
                    'type_of_ticket' => $approval_response['type_of_ticket'],
                    'user_id' => 10,
                    'comment'=> $approval_response['approver_comment']

                ]);
                $ticket->status = '4'; 
                $ticket->save();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Trạng thái ticket đã được cập nhật'
                ], 200);
            } else if ($ticket->status == '2' && ($ticket->support_type == '3' || $ticket->support_type == '4')){
                tracking_info_service::add(
                    $ticket->id,
                    10,
                    7,
                    'received approved response from Power Automate',
                );
                Comments_Model::create([
                    'ticket_id' => $approval_response['ticket_id'],
                    'type_of_ticket' => $approval_response['type_of_ticket'],
                    'user_id' => 10,
                    'comment'=> $approval_response['approver_comment']

                ]);
                $ticket->highest_approved_step = '2';
                $ticket->status = '3'; 
                $ticket->save();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Trạng thái ticket đã được cập nhật'
                ], 200);
            } else if ($ticket->status == '3' && ($ticket->support_type == '3' || $ticket->support_type == '4')){
                tracking_info_service::add(
                    $ticket->id,
                    10,
                    7,
                    'received approved response from Power Automate',
                );
                Comments_Model::create([
                    'ticket_id' => $approval_response['ticket_id'],
                    'type_of_ticket' => $approval_response['type_of_ticket'],
                    'user_id' => 10,
                    'comment'=> $approval_response['approver_comment']

                ]);
                $ticket->status = '4'; 
                $ticket->save();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Trạng thái ticket đã được cập nhật'
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Ticket không ở trạng thái chờ phê duyệt'
                ], 200);
            }
        }

        if ($approval_response['outcome'] === 'Approve level 0.5' && $approval_response['type_of_ticket'] === '7') 
        {
            $ticket = Invoice_Exceptional_Tickets_Model::find($approval_response['ticket_id']);
            if ($ticket->status == '2' && ($ticket->support_type == '3' || $ticket->support_type == '4')){
                tracking_info_service::add(
                    $ticket->id,
                    10,
                    7,
                    'received approved response from Power Automate',
                );
                Comments_Model::create([
                    'ticket_id' => $approval_response['ticket_id'],
                    'type_of_ticket' => $approval_response['type_of_ticket'],
                    'user_id' => 10,
                    'comment'=> $approval_response['approver_comment']

                ]);
                $ticket->save();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Trạng thái ticket đã được cập nhật'
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Ticket không ở trạng thái chờ phê duyệt'
                ], 200);
            }
        }

        if ($approval_response['outcome'] === 'Reject' && $approval_response['type_of_ticket'] === '7') 
        {
            $ticket = Invoice_Exceptional_Tickets_Model::find($approval_response['ticket_id']);
            if ($ticket->status == '2' && ($ticket->support_type == '1' || $ticket->support_type == '2')) {
                tracking_info_service::add(
                    $ticket->id,
                    10,
                    7,
                    'received rejected response from Power Automate',
                );
                Comments_Model::create([
                    'ticket_id' => $approval_response['ticket_id'],
                    'type_of_ticket' => $approval_response['type_of_ticket'],
                    'user_id' => 10,
                    'comment'=> $approval_response['approver_comment']

                ]);
                $ticket->status = '5'; 
                $ticket->save();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Trạng thái ticket đã được cập nhật'
                ], 200);
            } else if ($ticket->status == '2' && ($ticket->support_type == '3' || $ticket->support_type == '4')){
                tracking_info_service::add(
                    $ticket->id,
                    10,
                    7,
                    'received rejected response from Power Automate',
                );
                Comments_Model::create([
                    'ticket_id' => $approval_response['ticket_id'],
                    'type_of_ticket' => $approval_response['type_of_ticket'],
                    'user_id' => 10,
                    'comment'=> $approval_response['approver_comment']

                ]);
                $ticket->status = '5'; 
                $ticket->save();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Trạng thái ticket đã được cập nhật'
                ], 200);
            } else if ($ticket->status == '3' && ($ticket->support_type == '3' || $ticket->support_type == '4')){
                tracking_info_service::add(
                    $ticket->id,
                    10,
                    7,
                    'received rejected response from Power Automate',
                );
                Comments_Model::create([
                    'ticket_id' => $approval_response['ticket_id'],
                    'type_of_ticket' => $approval_response['type_of_ticket'],
                    'user_id' => 10,
                    'comment'=> $approval_response['approver_comment']

                ]);
                $ticket->status = '5'; 
                $ticket->save();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Trạng thái ticket đã được cập nhật'
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Ticket không ở trạng thái chờ phê duyệt'
                ], 200);
            }
        }

        

        if ($approval_response['outcome'] === 'Approve' && $approval_response['type_of_ticket'] === '9') 
        {
            $ticket = Out_Of_Office_Tickets_Model::find($approval_response['ticket_id']);
            if ($ticket->status == '2') {
                tracking_info_service::add(
                    $ticket->id,
                    10,
                    9,
                    'received approved response from Power Automate',
                );
                Comments_Model::create([
                    'ticket_id' => $approval_response['ticket_id'],
                    'type_of_ticket' => $approval_response['type_of_ticket'],
                    'user_id' => 10,
                    'comment'=> $approval_response['approver_comment']

                ]);
                $ticket->status = '3'; 
                $ticket->save();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Trạng thái ticket đã được cập nhật'
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Ticket không ở trạng thái chờ phê duyệt'
                ], 200);
            }
        }

        if ($approval_response['outcome'] === 'Reject' && $approval_response['type_of_ticket'] === '9') 
        {
            $ticket = Out_Of_Office_Tickets_Model::find($approval_response['ticket_id']);
            if ($ticket->status == '2') {
                tracking_info_service::add(
                    $ticket->id,
                    10,
                    9,
                    'received rejected response from Power Automate',
                );
                Comments_Model::create([
                    'ticket_id' => $approval_response['ticket_id'],
                    'type_of_ticket' => $approval_response['type_of_ticket'],
                    'user_id' => 10,
                    'comment'=> $approval_response['approver_comment']

                ]);
                $ticket->status = '4'; 
                $ticket->save();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Trạng thái ticket đã được cập nhật'
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Ticket không ở trạng thái chờ phê duyệt'
                ], 200);
            }
        }

        if ($approval_response['outcome'] === 'Approve' && $approval_response['type_of_ticket'] === '10') 
        {
            $ticket = Thermal_Event_Exceptional_Tickets_Model::find($approval_response['ticket_id']);
            if ($ticket->status == '2') {
                tracking_info_service::add(
                    $ticket->id,
                    10,
                    10,
                    'received approved response from Power Automate',
                );
                Comments_Model::create([
                    'ticket_id' => $approval_response['ticket_id'],
                    'type_of_ticket' => $approval_response['type_of_ticket'],
                    'user_id' => 10,
                    'comment'=> $approval_response['approver_comment']

                ]);
                $ticket->status = '3'; 
                $ticket->save();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Trạng thái ticket đã được cập nhật'
                ], 200);
            } else if ($ticket->status == '3') {
                tracking_info_service::add(
                    $ticket->id,
                    10,
                    10,
                    'received approved response from Power Automate',
                );
                Comments_Model::create([
                    'ticket_id' => $approval_response['ticket_id'],
                    'type_of_ticket' => $approval_response['type_of_ticket'],
                    'user_id' => 10,
                    'comment'=> $approval_response['approver_comment']

                ]);
                $ticket->status = '4'; 
                $ticket->save();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Trạng thái ticket đã được cập nhật'
                ], 200);
            } else{
                return response()->json([
                    'status' => 'error',
                    'message' => 'Ticket không ở trạng thái chờ phê duyệt'
                ], 200);
            }
        }

        if ($approval_response['outcome'] === 'Reject' && $approval_response['type_of_ticket'] === '10') 
        {
            $ticket = Thermal_Event_Exceptional_Tickets_Model::find($approval_response['ticket_id']);
            if ($ticket->status == '2' || $ticket->status == '3') {
                tracking_info_service::add(
                    $ticket->id,
                    10,
                    10,
                    'received rejected response from Power Automate',
                );
                Comments_Model::create([
                    'ticket_id' => $approval_response['ticket_id'],
                    'type_of_ticket' => $approval_response['type_of_ticket'],
                    'user_id' => 10,
                    'comment'=> $approval_response['approver_comment']

                ]);
                $ticket->status = '5'; 
                $ticket->save();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Trạng thái ticket đã được cập nhật'
                ], 200);
            }  else{
                return response()->json([
                    'status' => 'error',
                    'message' => 'Ticket không ở trạng thái chờ phê duyệt'
                ], 200);
            }
        }



        if ($approval_response['outcome'] === 'Approve' && $approval_response['type_of_ticket'] === '12') 
        {
            $export_details = HPS_Warehouse_Export_Details_Model::find($approval_response['ticket_id']);
            $asset = HPS_Warehouse_Model::where('asset_tag', $export_details->asset_tag)->first();
            if ($export_details->current_status == '1') {
                tracking_info_service::add(
                    $asset->id,
                    10,
                    12,
                    'received approved response from Power Automate',
                );
                Comments_Model::create([
                    'ticket_id' => $asset->id,
                    'type_of_ticket' => $approval_response['type_of_ticket'],
                    'user_id' => 10,
                    'comment'=> $approval_response['approver_comment']

                ]);
                $export_details->current_status = '2'; 
                $export_details->save();
                $asset->current_status = '3';
                $asset->current_hps_receipt = $export_details->hps_receipt;
                $asset->current_export_ticket_id = $export_details->id;
                $asset->save();

                return response()->json([
                    'status' => 'success',
                    'message' => 'Trạng thái ticket đã được cập nhật'
                ], 200);
            }  else{
                return response()->json([
                    'status' => 'error',
                    'message' => 'Ticket không ở trạng thái chờ phê duyệt'
                ], 200);
            }
        }


        if ($approval_response['outcome'] === 'Reject' && $approval_response['type_of_ticket'] === '12') 
        {
            $export_details = HPS_Warehouse_Export_Details_Model::find($approval_response['ticket_id']);
            $asset = HPS_Warehouse_Model::where('asset_tag', $export_details->asset_tag)->first();

            if ($export_details->current_status == '1') {
                tracking_info_service::add(
                    $asset->id,
                    10,
                    12,
                    'received rejected response from Power Automate',
                );
                Comments_Model::create([
                    'ticket_id' => $asset->id,
                    'type_of_ticket' => $approval_response['type_of_ticket'],
                    'user_id' => 10,
                    'comment'=> $approval_response['approver_comment']

                ]);
                $export_details->current_status = '5'; 
                $export_details->save();
                
                return response()->json([
                    'status' => 'success',
                    'message' => 'Trạng thái ticket đã được cập nhật'
                ], 200);
            }  else{
                return response()->json([
                    'status' => 'error',
                    'message' => 'Ticket không ở trạng thái chờ phê duyệt'
                ], 200);
            }
        }


        if ($approval_response['outcome'] === 'Approve' && $approval_response['type_of_ticket'] === '14') 
        {
            $ticket = Scrap_Request_Model::find($approval_response['ticket_id']);
            if ($ticket->status == '2') {
                tracking_info_service::add(
                    $ticket->id,
                    10,
                    14,
                    'received approved response from Power Automate',
                );
                Comments_Model::create([
                    'ticket_id' => $approval_response['ticket_id'],
                    'type_of_ticket' => $approval_response['type_of_ticket'],
                    'user_id' => 10,
                    'comment'=> $approval_response['approver_comment']

                ]);
                $ticket->status = '3'; 
                $ticket->save();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Trạng thái ticket đã được cập nhật'
                ], 200);
            }  else if ($ticket->status == '3') {
                tracking_info_service::add(
                    $ticket->id,
                    10,
                    14,
                    'received approved response from Power Automate',
                );
                Comments_Model::create([
                    'ticket_id' => $approval_response['ticket_id'],
                    'type_of_ticket' => $approval_response['type_of_ticket'],
                    'user_id' => 10,
                    'comment'=> $approval_response['approver_comment']

                ]);
                $ticket->status = '4'; 
                $ticket->save();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Trạng thái ticket đã được cập nhật'
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Ticket không ở trạng thái chờ phê duyệt'
                ], 200);
            }
        }


        if ($approval_response['outcome'] === 'Reject' && $approval_response['type_of_ticket'] === '14') 
        {
            $ticket = Thermal_Event_Exceptional_Tickets_Model::find($approval_response['ticket_id']);
            if ($ticket->status == '2' || $ticket->status == '3') {
                tracking_info_service::add(
                    $ticket->id,
                    10,
                    14,
                    'received rejected response from Power Automate',
                );
                Comments_Model::create([
                    'ticket_id' => $approval_response['ticket_id'],
                    'type_of_ticket' => $approval_response['type_of_ticket'],
                    'user_id' => 10,
                    'comment'=> $approval_response['approver_comment']

                ]);
                $ticket->status = '5'; 
                $ticket->save();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Trạng thái ticket đã được cập nhật'
                ], 200);
            }  else{
                return response()->json([
                    'status' => 'error',
                    'message' => 'Ticket không ở trạng thái chờ phê duyệt'
                ], 200);
            }
        }


        


        
        
    }

}
