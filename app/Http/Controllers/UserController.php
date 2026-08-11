<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Hash;
use App\Models\Attachments_Model;


use function Laravel\Prompts\alert;

class UserController extends Controller
{
    //
    public function login(){
        return view('login');
    }

    // public function authenticate(Request $request)
    // {

    //     $user_info_input = $request->validate([
    //         'Username' => 'required',
    //         'Password' => 'required',
    //     ]);
        
        

    //     if (auth()->attempt(['name' => $user_info_input['Username'], 'password' => $user_info_input['Password']])) {
    //         $request->session()->regenerate(); // Bảo mật: chống tấn công Fixation
    //         return redirect('/main-menu'); // Chuyển hướng đến trang chính sau khi đăng nhập thành công
    //     }
        
    //     return back()->withInput()->with([
    //     'login_error' => 'Sai username hoặc password'
    //     ]);
            
    // }

    

    public function authenticate(Request $request)
    {
        $user_info_input = $request->validate([
            'Username' => 'required',
            'Password' => 'required',
        ]);

        $user = User::where('name', $user_info_input['Username'])->first();

        // Có tài khoản nhưng bị khóa
        if ($user && $user->status === 'deactivate') {
            return back()->withInput()->with([
                'login_error' => 'Tài khoản của bạn đã bị vô hiệu hóa. Vui lòng liên hệ quản trị viên.'
            ]);
        }

        // Kiểm tra đăng nhập
        if (auth()->attempt([
            'name' => $user_info_input['Username'],
            'password' => $user_info_input['Password'],
        ])) {

            $request->session()->regenerate();

            return redirect('/main-menu');
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

    public function User_Profile()
    {
        $user = User::with('leader')
        ->findOrFail(auth()->id());

        $attachments = $user->active_attachments()
        ->paginate(10);
        return view('user-profile', compact('user', 'attachments'));
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

    public function Change_Password (Request $request) {
        try {
            $request->validate([
                'new_password' => [
                    'required',
                    'min:8',
                ],
                'confirm_new_password' => [
                    'required',
                    'same:new_password',
                ],
            ]);

            $user = auth()->user();

            $user->password = Hash::make($request->new_password);

            $user->save();

            return response()->json([
                'message' => 'Password changed successfully.',
                'success' => true,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to change password due to: ' . $e->getMessage() // Có thể bỏ ở môi trường production
            ], 500);
        }
    }

    public function Edit_Upload_Training_Ticket_Profile_Page(Request $request, $id)
    {
        try {
            

            $validate_data = $request->validate([
                'attachments.*' => 'file|max:20480|mimes:pdf'
            ]);

            if ($request->hasFile('attachments')) { //Kiểm tra xem có file nào được upload lên không

                foreach ($request->file('attachments') as $file) { //Duyệt qua từng file được upload lên
                    $originalName = $file->getClientOriginalName();
                    $folderPath = '5/' . $id . '/independent_certificates';
                    $filePath = $file->storeAs($folderPath, $originalName, 'attachments'); // Lưu file vào thư mục 'attachments' đã được cấu hình trong config/filesystems.php, với đường dẫn là 'attachments/1/{ticket_id}/{original_file_name}'

                    Attachments_Model::create([
                        'type_of_ticket' => 5, // Giả sử 1 là mã cho software ticket
                        'file_path' => $filePath,
                        'name' => $originalName, // Lưu tên gốc của file vào cơ sở dữ liệu
                        'user_id' => $id
                    ]);

                }
            }


            if ($request->has('delete_files')) {
                // Cập nhật tất cả các ID được tích chọn thành status = 0 trong 1 câu lệnh duy nhất
                Attachments_Model::whereIn('id', $request->input('delete_files'))->update(['status' => '0']);
            }


            return response()->json([
                'success' => true,
                'message' => 'Certificates edit/upload thành công ',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to edit/upload certificates due to ' . $e->getMessage(),
            ], 500);
        }
    }



    

    
}
