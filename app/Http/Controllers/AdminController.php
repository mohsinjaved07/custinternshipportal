<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function login(){
        return view("SuperAdmin.login");
    }

    public function registration(){
        return view("SuperAdmin.register");
    }

    public function register(Request $request){
        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required|unique:',
            'password' => 'required|string|min:6|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
            'department' => 'required',
            'contactno' => 'required|unique:|numeric|digits:11',
            'office' => 'required',
            'designation' => 'required',
            'officeExtension' => 'required'
        ]);
    }
}
