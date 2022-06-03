<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SuperAdmin;
use App\Models\Coordinator;
use App\Models\Term;
use App\Models\TermRegistered;
use App\Models\Student;
use App\Models\Announcement;
use App\Mail\CoordinatorAccountMail;
use Illuminate\Support\Facades\Mail;
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
            if ($request->password == $superadmin->password){
                session([
                    'id' => $superadmin->id
                ]);
                return Redirect('admin/dashboard');
            }
        }

        return Redirect()->back();
    }

    public function changePassword(){
        $id = session('id');
        if ($id){
            $superadmin = SuperAdmin::firstWhere('id', $id);
            return view('SuperAdmin.changepassword', compact('superadmin'));
        } else {
            return redirect('admin/login');
        }
    }

    public function password(Request $request){
        $id = session('id');
        if ($id){
            $validated = $request->validate([
                'curpassword' => 'required|max:100',
                'newpassword' => 'required|max:100|string|min:6|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
                'confirmpassword' => 'required|max:100|string|min:6|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
            ],
            [
                'curpassword.required' => 'Please enter current password',
                'newpassword.required' => 'Please enter new password',
                'confirmpassword.required' => 'Please enter confirm password',
            ]);

            $superadmin = SuperAdmin::firstWhere('id', $id);

            if ($superadmin){
                if ($request->curpassword == $superadmin->password){
                    if ($request->newpassword == $request->confirmpassword){
                        $superadmin->password = $request->newpassword;
                        $superadmin->save();
                        return Redirect('admin/login');
                    } else {
                        return Redirect()->back();
                    }
                } else {
                    return Redirect()->back();
                }
            }

        } else {
            return Redirect('/coordinator/loginForm');
        }
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

    public function coordinator(){
        $id = session('id');
        if($id){
            $superadmin = SuperAdmin::firstWhere('id', $id);
            $coordinator = Coordinator::all();
            return view("SuperAdmin.coordinator", compact('superadmin', 'coordinator'));
        } else {
            return redirect('admin/login');
        }
    }

    public function updateCoordinator($coordinatorid){
        $id = session('id');
        if($id){
            $superadmin = SuperAdmin::firstWhere('id', $id);
            $coordinator = Coordinator::firstWhere('id', $coordinatorid);
            return view("SuperAdmin.updatecoordinator", compact('superadmin', 'coordinator'));
        } else {
            return redirect('admin/login');
        }
    }

    public function deleteCoordinator($coordinatorid){
        $id = session('id');
        if($id){
            $superadmin = SuperAdmin::firstWhere('id', $id);
            $coordinator = Coordinator::firstWhere('id', $coordinatorid);
            return view("SuperAdmin.deletecoordinator", compact('superadmin', 'coordinator'));
        } else {
            return redirect('admin/login');
        }
    }

    public function postCoordinator(){
        $id = session('id');
        if($id){
            $superadmin = SuperAdmin::firstWhere('id', $id);
            return view("SuperAdmin.postcoordinator", compact('superadmin'));
        } else {
            return redirect('admin/login');
        }
    }

    public function addCoordinator(Request $request){
        $id = session('id');
        if($id){
            $validated = $request->validate([
                'name' => 'required',
                'email' => 'required|unique:coordinators',
                'password' => 'required|string|min:6|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
                'department' => 'required',
                'contactno' => 'required|unique:coordinators|numeric|digits:11',
                'office' => 'required',
            ]);

            $coordinator = new Coordinator;
            $coordinator->name = $request->name;
            $coordinator->email = $request->email;
            $coordinator->password = Hash::make($request->password);
            $coordinator->department = $request->department;
            $coordinator->contactno = $request->contactno;
            $coordinator->office = $request->office;
            $coordinator->save();

            $term = Term::all()->last();
            $students = Student::all();
            $coordinator = Coordinator::where('email', $request->email)->first();
            $termRegistered = TermRegistered::all()->where('term_name', $term->term_name);

            if(count($termRegistered) > 0) {
                foreach($termRegistered as $t){
                    $t->coordinator_id = $coordinator->id;
                    $t->save();
                }
            } else {
                if($term){
                    if($students){
                        foreach($students as $s){
                            $termRegistered = new TermRegistered;
                            $termRegistered->registration_no = $s->registration_no;
                            $termRegistered->term_name = $term->term_name;
                            $termRegistered->coordinator_id = $coordinator->id;
                            $termRegistered->save();
                        }
                    }
                }
            }
            Mail::to($request->email)->send(new CoordinatorAccountMail($request));
            return redirect('admin/coordinator');
        } else {
            return redirect('admin/login');
        }
    }

    public function changeCoordinator(Request $request){
        $id = session('id');
        if($id){
            $validated = $request->validate([
                'name' => 'required',
                'email' => 'required',
                'department' => 'required',
                'contactno' => 'required|numeric|digits:11',
                'office' => 'required',
            ]);
            
            $coordinator = Coordinator::firstWhere("email", $request->email);
            if($coordinator){
                $coordinator->where("email", $request->email)->update([
                    "name" => $request->name,
                    "email" => $request->email,
                    "department" => $request->department,
                    "contactno" => $request->contactno,
                    "office" => $request->office
                ]);
            } else {
                return redirect('admin/coordinator');    
            }

            return redirect('admin/coordinator');
        } else {
            return redirect('admin/login');
        }
    }

    public function removeCoordinator($coordinatorid){
        $id = session('id');
        if($id){
            $announcement = Announcement::all()->where("coordinator_id", $coordinatorid);
            if($announcement){
                foreach($announcement as $a){
                    $a->delete();
                }
            }

            $termRegistered = TermRegistered::all()->where("coordinator_id", $coordinatorid);
            if($termRegistered){
                foreach($termRegistered as $t){
                    $t->where("coordinator_id", $coordinatorid)->update([
                        'coordinator_id' => null
                    ]);
                }
            }

            $coordinator = Coordinator::firstWhere("id", $coordinatorid);
            $coordinator->delete();

            return redirect('admin/coordinator');
        }
    }

    public function logout(){
        session()->forget('id');
        return redirect('admin/login');
    }
}
