<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        // dd($request->all());
        $receipt = $request->input('receipt');
        // dd($receipt);

        // Nếu người dùng chưa nhập gì, trả về mảng rỗng
        if (empty($receipt)) {
            return response()->json([]);
        }

        // Truy vấn bảng software_tickets
        $softwareTickets = DB::table('eeg_software_tickets')
            ->select(
                DB::raw("'software' as type"), // Đánh dấu loại để làm link detail
                'id', 
                'ticket_receipt', 
                'created_at' // Hoặc bất kỳ trường nào bạn muốn hiển thị thêm
            )
            ->where('ticket_receipt', '=', $receipt)->get();


        // // Truy vấn bảng invoice_exceptional và gộp (UNION) với bảng trên
        // $results = DB::table('invoice_exceptional_tickets')
        //     ->select(
        //         DB::raw("'invoice' as type"), 
        //         'id', 
        //         'ticket_receipt', 
        //         'created_at'
        //     )
        //     ->where('ticket_receipt', '=', $receipt)
        //     ->unionAll($softwareTickets) // Dùng unionAll để gộp kết quả
        //     ->get();

        // // Trả về kết quả dạng JSON cho Frontend xử lý
        return response()->json($softwareTickets);
    }
}
