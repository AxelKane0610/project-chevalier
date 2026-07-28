<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Training_Tickets_Model;
use App\Models\Training_Courses_Model;
use App\Models\User;

use Illuminate\Support\Facades\DB;



class TrainingController extends Controller
{
    //
    public function Show_Pending_Tickets(){ 
        if (auth()->user()->hasRole('ROLE_SUPER_ADMIN') ) {
            $pending_tickets = Training_Tickets_Model::whereIn('status', ['1', '2', '3'])->get();
            $all_tickets = Training_Tickets_Model::all();
            return view('submit-training-menu', compact('pending_tickets', 'all_tickets'));
        } 
        

        
        
    }

    public function Show_Training_Ticket_Details($id){ 
        $ticket_details = Training_Tickets_Model::with('user_owner', 'active_attachments','ticket_tracking_info','ticket_comments.attachments', 'ticket_comments.user', 'training_courses')->findOrFail($id);
        return view('training-ticket-details', compact('ticket_details'));

        
        
    }

    public function Request_Training(Request $request){ 
        $request->validate([
        'course_id' => 'required|array|min:1',
        'course_name' => 'required|array|min:1',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
    ]);

    DB::transaction(function () use ($request) {

        // Lấy Training No lớn nhất
        $trainingNo = (Training_Courses_Model::max('training_no') ?? 0) + 1;

        // Lưu từng dòng
        foreach ($request->course_id as $index => $courseId) {

            Training_Courses_Model::create([
                'training_no' => $trainingNo,
                'course_id' => $courseId,
                'course_name' => $request->course_name[$index],
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
            ]);
        }

        $users = User::whereIn('team', ['2', '3'])->get();
            foreach ($users as $user) {

            Training_Tickets_Model::create([
                'user_id' => $user->id,
                'training_no' => $trainingNo,
                'status' => '2', // hoặc 'Pending'
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
            ]);
        }
    });

        return redirect()->back()->with('success', 'Training Request created successfully.');
        
    }
}
