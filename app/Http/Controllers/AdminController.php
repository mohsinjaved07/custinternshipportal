<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SuperAdmin;
use App\Models\Coordinator;
use App\Models\Term;
use App\Models\TermRegistered;
use App\Models\Student;
use App\Models\StudentAccount;
use App\Models\StudentDocs;
use App\Models\Announcement;
use App\Mail\CoordinatorAccountMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;

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
                    'adminid' => $superadmin->id
                ]);
                return Redirect('admin/dashboard');
            }
        }

        return Redirect()->back();
    }

    public function changePassword(){
        $id = session('adminid');
        if ($id){
            $superadmin = SuperAdmin::firstWhere('id', $id);
            return view('SuperAdmin.changepassword', compact('superadmin'));
        } else {
            return redirect('admin/login');
        }
    }

    public function password(Request $request){
        $id = session('adminid');
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
        $id = session('adminid');
        if($id){
            $term = Term::all()->last();
            $superadmin = SuperAdmin::firstWhere('id', $id);
            $coordinator = TermRegistered::firstWhere('term_name', $term->term_name);
            return view("SuperAdmin.dashboard", compact('superadmin', 'coordinator', 'term'));
        } else {
            return redirect('admin/login');
        }
    }

    public function coordinator(){
        $id = session('adminid');
        if($id){
            $term = Term::all()->last();
            $superadmin = SuperAdmin::firstWhere('id', $id);
            $coordinator = Coordinator::all();
            $maincoordinator = TermRegistered::firstWhere('term_name', $term->term_name);
            return view("SuperAdmin.coordinator", compact('superadmin', 'coordinator', 'term', 'maincoordinator'));
        } else {
            return redirect('admin/login');
        }
    }

    public function postCoordinator(){
        $id = session('adminid');
        if($id){
            $superadmin = SuperAdmin::firstWhere('id', $id);
            return view("SuperAdmin.postcoordinator", compact('superadmin'));
        } else {
            return redirect('admin/login');
        }
    }

    public function addCoordinator($coordinatorid){
        $id = session('adminid');
        if($id){
            $term = Term::all()->last();
            $students = Student::all();
            $termRegistered = TermRegistered::all()->where('term_name', $term->term_name);

            if(count($termRegistered) > 0) {
                foreach($termRegistered as $t){
                    $t->update([
                        "coordinator_id" => $coordinatorid
                    ]);
                }
            } else {
                if($term){
                    if($students){
                        foreach($students as $s){
                            $termRegistered = new TermRegistered;
                            $termRegistered->registration_no = $s->registration_no;
                            $termRegistered->term_name = $term->term_name;
                            $termRegistered->coordinator_id = $coordinatorid;
                            $termRegistered->save();

                            $studentAcc = StudentAccount::all();
                            foreach($studentAcc as $sa){
                                $sa->where('registration_no', $s->registration_no)->delete();
                            }

                            $studentdocs = StudentDocs::all();
                            foreach($studentdocs as $sd){
                                $sd->where('registration_no', $s->registration_no)->delete();
                            }

                            $term->where('term_name', $term->term_name)->update([
                                "internship_plan" => null
                            ]);
                        }

                        $announcement = Announcement::all();
                        foreach($announcement as $a){
                            if($a){
                                $a->delete();
                            }
                        }
                    }
                }
            }

            $coordinator = Coordinator::all();
            foreach($coordinator as $c){
                $c->update([
                    'password' => null,
                ]);
            }

            $coordinator = Coordinator::firstWhere("id", $coordinatorid);
            $password = $this->generate_letter_pass();

            $coordinator->update([
                'password' => Crypt::encryptString($password),
            ]);

            Mail::to($coordinator->email)->send(new CoordinatorAccountMail($coordinator, $password));
            return redirect('admin/coordinator');
        } else {
            return redirect('admin/login');
        }
    }

    function generate_letter_pass(){
        $random = '';
        for($i = 0; $i < 6; $i++){
            $random .= rand(0, 1) ? rand(0, 9) : chr(rand(ord('a'), ord('z')));
        }
        return $random;
    }

    public function removeCoordinator(Request $request){
        $id = session('adminid');
        if($id){
            $term = Term::all()->last();
            $termRegistered = TermRegistered::all()->where("coordinator_id", $request->coordinatorid);
            if($termRegistered){
                foreach($termRegistered as $t){
                    $t->where("term_name", $term->term_name)->update([
                        'coordinator_id' => null
                    ]);
                }
            }

            $coordinator = Coordinator::firstWhere("id", $request->coordinatorid);
            $coordinator->update([
                'password' => null,
            ]);

            return redirect('admin/coordinator');
        }
    }

    public function logout(){
        session()->forget('adminid');
        return redirect('admin/login');
    }
}
