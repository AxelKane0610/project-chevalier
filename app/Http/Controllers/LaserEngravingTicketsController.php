<?php

namespace App\Http\Controllers;

use App\Models\Laser_Engraving_Tickets_Model;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request; 

class LaserEngravingTicketsController extends Controller
{
    //
    public function Show_Pending_Tickets()
    {
        $tickets = Laser_Engraving_Tickets_Model::all();
        return view('laser-engraving-menu', compact('tickets'));
    }

    public function Create_Laser_Engraving_Ticket(Request $request)
    {
        // Validate dữ liệu đầu vào
        $validatedData = $request->validate([
            'ticket_reciept' => 'required|string|max:255',
            'priority' => 'required|in:1,2,3,4',
            'info_base' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $validatedData['ticket_reciept'] = strip_tags($validatedData['ticket_reciept']);
        $validatedData['info_base'] = strip_tags($validatedData['info_base']);
        $validatedData['description'] = strip_tags($validatedData['description']);

        // Tạo một ticket mới trong cơ sở dữ liệu
        $ticket = Laser_Engraving_Tickets_Model::create([
            'user_id' => auth()->id(), // Lấy ID của người dùng hiện tại
            'receipt' => $validatedData['ticket_reciept'],
            'priority' => $validatedData['priority'],
            'info_base' => $validatedData['info_base'],
            'description' => $validatedData['description'],
            // Thêm các trường khác nếu cần thiết
        ]);

        // Trả về phản hồi (có thể là JSON hoặc chuyển hướng)
        return response()->json(['message' => 'Ticket created successfully', 'ticket' => $ticket], 201);
    }
}
