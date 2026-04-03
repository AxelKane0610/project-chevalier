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
        
        // User::create([
        //     'name'     => $user_info_input['Username'], // Map Username vào cột name
        //     'password' => bcrypt($user_info_input['Password']),
        // ]);

        if (auth()->attempt(['name' => $user_info_input['Username'], 'password' => $user_info_input['Password']])) {
            $request->session()->regenerate(); // Bảo mật: chống tấn công Fixation
            return redirect()->intended('/main-menu'); // Chuyển hướng đến trang chính sau khi đăng nhập thành công
        }
        
        return back()->withInput()->with([
        'login_error' => 'Sai username hoặc password'
        ]);
            
    }

    

    
}
