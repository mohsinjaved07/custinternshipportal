<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\StudentAccount;
use App\Models\StudentDocs;
use App\Models\TermRegistered;
use App\Models\Term;
use App\Models\Announcement;
use App\Models\Organization;
use App\Models\Supervisor;
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

        return Redirect()->back()->with("message", "Invalid username and password.");
    }

    public function downloadrecletter(){
        $registration_no = session('registration_no');
        if ($registration_no){
            $studentdocs = StudentDocs::where('registration_no', $registration_no)->first();
            
            if ($studentdocs){
                return response()->download($studentdocs->recommendation_letter);
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
            }
        } else {
            return Redirect("/student/loginForm");
        }
    }

    public function dashboardPage(){
        $registration_no = session('registration_no');
        if ($registration_no){
            $announcement = Announcement::where('registration_no', $registration_no)->get();
            $student = Student::where('registration_no', $registration_no)->first();
            $term = Term::all()->last();
            $root = TermRegistered::where([['registration_no', $registration_no], ['term_name', $term->term_name]])->first();
            return view('Student.dashboard', compact('student', 'term', 'announcement', 'root'));
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
                        'one_time_auth' => 'Active'
                    ]);
                } else {
                    return Redirect()->back()->with("message", "Password mismatched.");    
                }
            } else {
                return Redirect()->back()->with("message", "Invalid current password.");
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
            return Redirect()->back()->with("message", "Password request has been sent. Please check your email.");
        } else {
            return Redirect()->back()->with("message", "Sorry, you're email is not registered.");
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
                return Redirect()->back()->with("message", "Password mismatched.");
            }
        }

        return Redirect('student/loginForm');

    }

    public function internshipInfo(){
        $registration_no = session('registration_no');
        if ($registration_no){
            $student = Student::where('registration_no', $registration_no)->first();
            $term = Term::all()->last();
            $root = TermRegistered::where([['registration_no', $registration_no], ['term_name', $term->term_name]])->first();
            return view('Student.internshipinfo', compact('student', 'term', 'root'));
        } else {
            return Redirect("/student/loginForm");
        }
    }

    public function uploadcompletioncertificate(){
        $registration_no = session('registration_no');
        if ($registration_no){
            $student = Student::where('registration_no', $registration_no)->first();
            $term = Term::all()->last();
            $root = TermRegistered::where([['registration_no', $registration_no], ['term_name', $term->term_name]])->first();
            return view('Student.uploadcompletioncertificate', compact('student', 'term', 'root'));
        } else {
            return Redirect("/student/loginForm");
        }
    }

    public function internshipcompletioncertificate($regno, Request $request){
        $registration_no = session('registration_no');
        if ($registration_no){
            $validated = $request->validate([
                'internshipCompletionCertificate' => 'required|mimes:pdf,png,jpg',
                'internshipReport' => 'required|mimes:pdf',
                'internshipPerforma' => 'required|mimes:pdf'
            ],
            [
                'internshipCompletionCertificate.required' => 'Please upload internship completion certificate.',
                'internshipReport.required' => 'Please upload report.',
                'internshipPerforma.required' => 'Please upload performa.',
            ]);
            
            $offerletterfile = $request->file("internshipCompletionCertificate");
            $file_ext = strtolower($offerletterfile->getClientOriginalExtension());
            $certificate_file_name = $regno.'.'.$file_ext;
            $offerletterfile->move("internship_completion_certificate", $certificate_file_name);

            $offerletterfile = $request->file("internshipReport");
            $file_ext = strtolower($offerletterfile->getClientOriginalExtension());
            $report_file_name = $regno.'.'.$file_ext;
            $offerletterfile->move("internship_reports", $report_file_name);

            $offerletterfile = $request->file("internshipPerforma");
            $file_ext = strtolower($offerletterfile->getClientOriginalExtension());
            $performa_file_name = $regno.'.'.$file_ext;
            $offerletterfile->move("evaluation_performas", $performa_file_name);
            
            $term = Term::all()->last();
            $termregistered = TermRegistered::where([['registration_no', $regno], ['term_name', $term->term_name]])->first();
            if($termregistered){
                if(isset($termregistered->document_uploaded_date)){
                    $termregistered->where([['registration_no', $registration_no], ['term_name', $term->term_name]])->update([
                        'internship_completion_certificate' => "internship_completion_certificate/".$certificate_file_name,
                        'internship_report' => "internship_reports/".$report_file_name,
                        'internship_evaluation_performa' => "evaluation_performas/".$performa_file_name,
                        'document_status' => 'pending'
                    ]);
                } else {
                    $termregistered->where([['registration_no', $registration_no], ['term_name', $term->term_name]])->update([
                        'internship_completion_certificate' => "internship_completion_certificate/".$certificate_file_name,
                        'internship_report' => "internship_reports/".$report_file_name,
                        'internship_evaluation_performa' => "evaluation_performas/".$performa_file_name,
                        'document_status' => 'pending',
                        'document_uploaded_date' => Carbon::now()
                    ]);
                }
            }
            return Redirect()->back();
        } else {
            return Redirect("/student/loginForm");
        }
    }

    public function setOrganizationDetails(Request $request){
        $registration_no = session('registration_no');
        if ($registration_no){
            $validated = $request->validate([
                'orgntn' => 'required|max:100|regex:/[0-9]{7}\-[0-9]{1}/',
                'orgname' => 'required|max:100',
                'orgemail' => 'required|max:100|regex:/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/',
                'orgcontact' => 'required|numeric|digits:11',
                'orgaddress' => 'required|max:100',
                'orgwebsite' => 'required|max:100',
                'supervisorname' => 'required|max:100',
                'supervisoremail' => 'required|max:100|regex:/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/',
                'supervisordesignation' => 'required|max:100',
                'supervisorcontact' => 'required|numeric|digits:11',
                'supervisordepartment' => 'required|max:100',
                'offerLetter' => 'required|mimes:pdf',
                'startDate' => 'required',
                'endDate' => 'required'
            ],
            [
                'orgntn.required' => 'Please enter organization NTN',
                'orgntn.regex' => 'This is not a valid NTN no.',
                'orgname.required' => 'Please enter organization name',
                'orgemail.required' => 'Please enter organization email',
                'orgemail.regex' => 'This is not a valid email',
                'orgcontact.required' => 'Please enter organization contact',
                'orgaddress.required' => 'Please enter organization address',
                'orgwebsite.required' => 'Please enter organization website',
                'supervisorname.required' => 'Please enter supervisor name',
                'supervisoremail.required' => 'Please enter supervisor email',
                'supervisoremail.regex' => 'This is not a valid email',
                'supervisordesignation.required' => 'Please enter supervisor designation',
                'supervisorcontact.required' => 'Please enter supervisor contact',
                'supervisordepartment.required' => 'Please enter supervisor department',
                'offerLetter.required' => 'Please upload offer letter',
            ]);
            
            $offerletterfile = $request->file("offerLetter");
            $file_ext = strtolower($offerletterfile->getClientOriginalExtension());
            $file_name = $registration_no.'.'.$file_ext;
            $offerletterfile->move("offer_letters", $file_name);

            $term = Term::all()->last();
            $termregistered = TermRegistered::where([['registration_no', $registration_no], ['term_name', $term->term_name]])->first();
            if ($termregistered){
                $termregistered->where([['registration_no', $registration_no], ['term_name', $term->term_name]])->update([
                    'organization_ntn_no' => strtoupper($request->orgntn),
                    'organization_name' => $request->orgname,
                    'organization_email' => $request->orgemail,
                    'organization_contact' => $request->orgcontact,
                    'organization_address' => $request->orgaddress,
                    'organization_website' => $request->orgwebsite,
                    'supervisor_name' => $request->supervisorname,
                    'supervisor_email' => $request->supervisoremail,
                    'supervisor_designation' => $request->supervisordesignation,
                    'supervisor_contact' => $request->supervisorcontact,
                    'supervisor_department' => $request->supervisordepartment,
                    'offer_letter' => "offer_letters/".$file_name,
                    'offer_letter_uploaded_date' => Carbon::now(),
                    'start_date' => $request->startDate,
                    'end_date' => $request->endDate,
                    'offer_letter_status' => 'pending'
                ]);
            }

            $org = Organization::where('organization_ntn_no', $request->orgntn)->first();
            if(!$org){
                $org = new Organization;
                $org->organization_ntn_no = strtoupper($request->orgntn);
                $org->organization_name = $request->orgname;
                $org->organization_email = $request->orgemail;
                $org->organization_contact = $request->orgcontact;
                $org->organization_address = $request->orgaddress;
                $org->organization_website = $request->orgwebsite;
                $org->save();
            }

            $supervisor = Supervisor::where('supervisor_name', $request->supervisorname)->first();
            if(!$supervisor){
                $supervisor = new Supervisor;
                $supervisor->supervisor_name = $request->supervisorname;
                $supervisor->supervisor_email = $request->supervisoremail;
                $supervisor->supervisor_designation = $request->supervisordesignation;
                $supervisor->supervisor_contact = $request->supervisorcontact;
                $supervisor->supervisor_department = $request->supervisordepartment;
                $supervisor->organization_ntn_no = strtoupper($request->orgntn);
                $supervisor->save();
            }
        } else {
            return Redirect('student/loginForm');
        }

        return Redirect()->back();
    }

}
