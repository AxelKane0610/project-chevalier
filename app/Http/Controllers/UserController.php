<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use function Laravel\Prompts\alert;

class UserController extends Controller
{
    //
    public function login(){
        return view('login');
    }

    public function authenticate(Request $request)
    {

        $user_info_input = $request->validate([
            'Username' => 'required',
            'Password' => 'required',
        ]);
        
        

        if (auth()->attempt(['name' => $user_info_input['Username'], 'password' => $user_info_input['Password']])) {
            $request->session()->regenerate(); // Bảo mật: chống tấn công Fixation
            return redirect('/main-menu'); // Chuyển hướng đến trang chính sau khi đăng nhập thành công
        }
        
        return back()->withInput()->with([
        'login_error' => 'Sai username hoặc password'
        ]);
            
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    public function index(){
        $users = User::with('leader')->get();
        return view('/subk-management-menu', compact('users'));
    }

    public function Create_New_User(Request $request){
        // dd($request->all());
        $user_info_input = $request->validate([
            'username' => 'required',
            'password' => 'required',
            'fullname'   => 'required', // Bắt buộc phải khai báo ở đây
            'team' => 'nullable',
            'leader' => 'nullable',
            'site' => 'required',
            'email' => 'required',
            'learner_id' => 'required',
            'phone_number' => 'nullable',
            'roles' => 'required|array',
        ]);

        
        User::create([
            'name'     => $user_info_input['Username'], // Map Username vào cột name
            'password' => bcrypt($user_info_input['Password']),
            'fullname' => $user_info_input['Fullname'],
            'site_id' => $user_info_input['Site'], 
            'leader_id' => User::where('fullname', $user_info_input['Leader'])->first()->id ?? null,
            'email' => $user_info_input['Email'],
            'learner_id' => $user_info_input['Learner_Id'],
            'roles' => $user_info_input['roles'],
        ]);

        return redirect()->back()->with('success', 'Tạo người dùng thành công!');
    }

    public function User_Profile(){
        return view('/user-profile');
    }

    

    
}
