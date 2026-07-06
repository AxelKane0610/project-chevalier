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
        try {
            $user_info_input = $request->validate([
                'name' => 'required',
                'password' => 'required',
                'fullname'   => 'required', // Bắt buộc phải khai báo ở đây
                'team' => 'nullable',
                'leader_id' => 'nullable',
                'site_id' => 'required',
                'email' => 'required',
                'learner_id' => 'required',
                'phone_number' => 'nullable',
                'roles' => 'required|array',
            ]);

            $user_info_input['name'] = strip_tags($user_info_input['name']); // Chuyển name thành chữ thường
            $user_info_input['password'] = bcrypt(strip_tags($user_info_input['password']));
            $user_info_input['fullname'] = strip_tags($user_info_input['fullname']);
            $user_info_input['email'] = strip_tags($user_info_input['email']);
            $user_info_input['learner_id'] = strip_tags($user_info_input['learner_id']);
            $user_info_input['phone_number'] = strip_tags($user_info_input['phone_number']);
            $user_info_input['leader_id'] = User::where('email', $user_info_input['leader_id'])->first()->id ?? null;

            
            $new_user = User::create($user_info_input);

            return response()->json([
                'message' => 'User created successfully.',
                'success' => true,
                ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create ticket due to: ' . $e->getMessage() // Có thể bỏ ở môi trường production
            ], 500);
        }
    }

    public function Edit_User_Info(Request $request, $id){
        try {
            $user_info_input = $request->validate([
                'fullname'   => 'required', // Bắt buộc phải khai báo ở đây
                'team' => 'nullable',
                'leader_id' => 'nullable',
                'site_id' => 'required',
                'email' => 'required',
                'learner_id' => 'required',
                'phone_number' => 'nullable',
                'roles' => 'required|array',
            ]);

            $user_info_input['fullname'] = strip_tags($user_info_input['fullname']);
            $user_info_input['email'] = strip_tags($user_info_input['email']);
            $user_info_input['learner_id'] = strip_tags($user_info_input['learner_id']);
            $user_info_input['phone_number'] = strip_tags($user_info_input['phone_number']);
            $user_info_input['leader_id'] = User::where('email', $user_info_input['leader_id'])->first()->id ?? null;

            
            $user = User::findOrFail($id);
            $user->update($user_info_input);

            return response()->json([
                'message' => 'User updated successfully.',
                'success' => true,
                ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user due to: ' . $e->getMessage() // Có thể bỏ ở môi trường production
            ], 500);
        }
    }

    public function User_Profile(){
        return view('/user-profile');
    }

    public function Reset_Password ($id){
        try {
            $user = User::findOrFail($id);
            $defaultPassword = env('DEFAULT_USER_PASSWORD');; // Password mặc định
            $user->password = bcrypt($defaultPassword);
            $user->save();
            return response()->json([
                'message' => 'Password reset successfully.',
                'success' => true,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reset password due to: ' . $e->getMessage() // Có thể bỏ ở môi trường production
            ], 500);
        }
    }

    

    
}
