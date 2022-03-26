<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\StudentAccount;
use App\Models\StudentDocs;
use App\Models\TermRegistered;
use App\Models\Term;
use App\Models\InternConfirm;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;
use App\Mail\StudentForgotPwd;
use App\Mail\InternConfirmation;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\IOFactory;
use Carbon\Carbon;

class StudentController extends Controller
{
    public function login(Request $request){
        $validated = $request->validate([
            'loginid' => 'required|max:100',
            'password' => 'required|max:100',
        ],
        [
            'loginid.required' => 'Please enter login',
            'password.required' => 'Please enter password',
        ]);

        $studentaccount = StudentAccount::where('login_id', strtolower($request->loginid))->first();

        if ($studentaccount){
            if ($request->password == $studentaccount->password){
                session(['registration_no' => $studentaccount->registration_no]);
                if ($studentaccount->one_time_auth == null){
                    return Redirect('/student/accountsettings');
                } else {
                    return Redirect('/student/dashboard');
                }
            }
        }

        return Redirect()->back();
    }

    public function downloadrecletter(){
        $registration_no = session('registration_no');
        if ($registration_no){
            $studentdocs = StudentDocs::where('registration_no', $registration_no)->first();

            if ($studentdocs){
                return response()->download($studentdocs->recommendation_letter);
            } else {
                return Redirect()->back();
            }
        
        } else {
            return Redirect("/student/loginForm");
        }
    }

    public function downloadinternshipplan(){
        $registration_no = session('registration_no');
        if ($registration_no){
            $studentdocs = StudentDocs::where('registration_no', $registration_no)->first();
            
            if($studentdocs){
                return response()->download($studentdocs->internship_plan);
            } else {
                return Redirect()->back();
            }
        
        } else {
            return Redirect("/student/loginForm");
        }
    }

    public function dashboardPage(){
        $registration_no = session('registration_no');
        if ($registration_no){
            $student = Student::where('registration_no', $registration_no)->first();
            $term = Term::all()->last();
            $studentintern = InternConfirm::where('registration_no', $registration_no)->first();
            return view('Student.dashboard', compact('student', 'term', 'studentintern'));
        } else {
            return Redirect("/student/loginForm");
        }
    }

    public function loginForm(){
        $registration_no = session('registration_no');
        if($registration_no){
            return redirect('student/dashboard');
        } else {
            return view('Student.login');
        }
    }

    public function logout(){
        session()->forget('registration_no');
        return Redirect('/student/loginForm');
    }

    public function getplan(){
        $registration_no = session('registration_no');
        if ($registration_no){
            $term = Term::all()->last();
            $student = Student::where('registration_no', $registration_no)->first();
            return view('Student.plan', compact('student', 'term'));
        } else {
            return Redirect("/student/loginForm");
        }
    }

    public function accountsettings(){
        $registration_no = session('registration_no');
        if ($registration_no){
            $student = Student::where('registration_no', $registration_no)->first();
            $term = Term::all()->last();
            return view('Student.accountinfo', compact('student', 'term'));
        } else {
            return Redirect("/student/loginForm");
        }
    }

    public function setpassword(Request $request){
        $registration_no = session('registration_no');
        if ($registration_no){
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

            $studentaccount = StudentAccount::where('registration_no', $registration_no)->first();
            if($studentaccount->password == $request->curpassword){
                if($request->newpassword == $request->confirmpassword){
                    $studentaccount->where('registration_no', $registration_no)->update([
                        'password' => $request->confirmpassword,
                        'one_time_auth' => 'authenticated'
                    ]);
                } else {
                    return Redirect()->back();    
                }
            } else {
                return Redirect()->back();
            }
            session()->forget('registration_no');
        }
        
        return Redirect("/student/loginForm");
    }

    public function forgotpasswordlink(){
        return view('Student.forgotpasswordlink');
    }

    public function sendforgotpasswordemail(Request $request){
        $validated = $request->validate([
            'email' => 'required|max:100',
        ],
        [
            'email.required' => 'Please enter email',
        ]);

        $student = Student::where('email', $request->email)->first();

        if($student){
            Mail::to($student->email)->send(new StudentForgotPwd($student));
            return Redirect()->back();
        } else {
            return Redirect()->back();
        }
    }

    public function forgotpassword($registrationno){
        $student = Student::where('registration_no', Crypt::decryptString($registrationno))->first();

        if($student){
            return view('Student.forgotpassword', compact('student'));
        } else {   
            return Redirect('student/loginForm');
        }
    }

    public function setforgotpassword(Request $request, $registrationno){
        $validated = $request->validate([
            'newpassword' => 'required|max:100|string|min:6||regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
            'confirmpassword' => 'required|max:100|string|min:6|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/'
        ],
        [
            'newpassword.required' => 'Please enter new password',
            'confirmpassword.required' => 'Please enter confirm password'
        ]);
        
        $studentaccount = StudentAccount::where('registration_no', $registrationno)->first();

        if($studentaccount){
             if($request->newpassword == $request->confirmpassword){
                $studentaccount->where('registration_no', $registrationno)->update([
                    'password' => $request->confirmpassword,
                    'one_time_auth' => 'authenticated'
                ]);
            } else {
                return Redirect()->back();
            }
        }

        return Redirect('student/loginForm');

    }

    public function internshipInfo(){
        $registration_no = session('registration_no');
        if ($registration_no){
            $student = Student::where('registration_no', $registration_no)->first();
            $term = Term::all()->last();
            $studentintern = InternConfirm::where('registration_no', $registration_no)->first();
            return view('Student.internshipinfo', compact('student', 'term', 'studentintern'));
        } else {
            return Redirect("/student/loginForm");
        }
    }

    public function setOrganizationDetails(Request $request){
        $registration_no = session('registration_no');
        if ($registration_no){
            $validated = $request->validate([
                'orgntn' => 'required|max:100',
                'orgname' => 'required|max:100',
                'orgemail' => 'required|max:100',
                'orgcontact' => 'required|numeric|digits:11',
                'orgaddress' => 'required|max:100',
                'orgwebsite' => 'required|max:100',
                'supervisorname' => 'required|max:100',
                'supervisoremail' => 'required|max:100',
                'supervisordesignation' => 'required|max:100',
                'supervisorcontact' => 'required|numeric|digits:11'
            ],
            [
                'orgntn.required' => 'Please enter organization NTN',
                'orgname.required' => 'Please enter organization name',
                'orgemail.required' => 'Please enter organization email',
                'orgcontact.required' => 'Please enter organization contact',
                'orgaddress.required' => 'Please enter organization address',
                'orgwebsite.required' => 'Please enter organization website',
                'supervisorname.required' => 'Please enter supervisor name',
                'supervisoremail.required' => 'Please enter supervisor email',
                'supervisordesignation.required' => 'Please enter supervisor designation',
                'supervisorcontact.required' => 'Please enter supervisor contact'
            ]);

            $term = Term::all()->last();
            $termregistered = TermRegistered::where([['registration_no', $registration_no], ['term_name', $term->term_name]])->first();
            if ($termregistered){
                $termregistered->where([['registration_no', $registration_no], ['term_name', $term->term_name]])->update([
                    'organization_ntn_no' => $request->orgntn,
                    'organization_name' => $request->orgname,
                    'organization_email' => $request->orgemail,
                    'organization_contact' => $request->orgcontact,
                    'organization_address' => $request->orgaddress,
                    'organization_website' => $request->orgwebsite,
                    'supervisor_name' => $request->supervisorname,
                    'supervisor_email' => $request->supervisoremail,
                    'supervisor_designation' => $request->supervisordesignation,
                    'supervisor_contact' => $request->supervisorcontact
                ]);
            }
            
            $link = Crypt::encryptString($request->orgname);
            $internconfirm = InternConfirm::where('registration_no', $registration_no)->first();
            if($internconfirm){
                $internconfirm->where('registration_no', $registration_no)->update([
                    'link' => $link,
                    'expire_date'=> Carbon::now()->addDays(7),
                    'status' => NULL
                ]);
            } else {
                $internconfirm = new InternConfirm;
                $internconfirm->registration_no = $registration_no;
                $internconfirm->link = $link;
                $internconfirm->expire_date = Carbon::now()->addDays(7);
                $internconfirm->status = NULL;
                $internconfirm->save();
            }
            Mail::to($request->supervisoremail)->send(new InternConfirmation($link, $internconfirm));
        } else {
            return Redirect('student/loginForm');
        }

        return Redirect()->back();
    }

}
