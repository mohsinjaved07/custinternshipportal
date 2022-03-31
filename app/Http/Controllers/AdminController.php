<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SuperAdmin;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function login(){
        return view("SuperAdmin.login");
    }

    public function validation(Request $request){
        $validated = $request->validate([
            'email' => 'required|max:100',
            'password' => 'required|max:100',
        ],
        [
            'email.required' => 'Please enter email',
            'password.required' => 'Please enter password',
        ]);

        $superadmin = SuperAdmin::where('email', $request->email)->first();

        if ($superadmin){
            if (Hash::check($request->password, $superadmin->password)){
                session([
                    'id' => $superadmin->id
                ]);
                return Redirect('admin/dashboard');
            }
        }

        return Redirect()->back();
    }

    public function registration(){
        return view("SuperAdmin.register");
    }

    public function register(Request $request){
        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required|unique:super_admins',
            'password' => 'required|string|min:6|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
            'department' => 'required',
            'contactno' => 'required|unique:super_admins|numeric|digits:11',
            'office' => 'required',
            'designation' => 'required',
            'officeExtension' => 'required'
        ]);

        $superadmin = new SuperAdmin;
        $superadmin->name = $request->name;
        $superadmin->email = $request->email;
        $superadmin->password = Hash::make($request->password);
        $superadmin->department = $request->department;
        $superadmin->contactno = $request->contactno;
        $superadmin->office = $request->office;
        $superadmin->designation = $request->designation;
        $superadmin->office_extension = $request->officeExtension;
        $superadmin->save();

        return redirect('admin/login');
    }

    public function dashboard(){
        $id = session('id');
        if($id){
            $superadmin = SuperAdmin::firstWhere('id', $id);
            return view("SuperAdmin.dashboard", compact('superadmin'));
        } else {
            return redirect('admin/login');
        }
    }
}
