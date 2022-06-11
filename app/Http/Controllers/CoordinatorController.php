<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Coordinator;
use App\Models\Student;
use App\Models\StudentAccount;
use App\Models\StudentDocs;
use App\Models\TermRegistered;
use App\Models\Term;
use App\Models\Announcement;
use App\Models\Organization;
use App\Models\Supervisor;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use App\Mail\LetterMail;
use App\Mail\LoginInfoMail;
use App\Mail\AnnouncementMail;
use Illuminate\Support\Facades\Mail;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord;
use PhpOffice\PhpSpreadsheet;
use Carbon\Carbon;
use App\Mail\CoordinatorForgotPwd;

class CoordinatorController extends Controller
{    
    public function loginForm(){
        return view('Coordinator.login');
    }

    public function login(Request $request){
        $validated = $request->validate([
            'email' => 'required|max:100',
            'password' => 'required|max:100',
        ],
        [
            'email.required' => 'Please enter email',
            'password.required' => 'Please enter password',
        ]);

        $coordinator = Coordinator::where('email', $request->email)->first();

        if ($coordinator){
            if (Hash::check($request->password, $coordinator->password)){
                session([
                    'id' => $coordinator->id,
                    'term' => Term::all()->last()->term_name,
                    'output' => null
                ]);
                return Redirect('/coordinator/dashboard');
            }
        }

        return Redirect()->back()->with('message', 'Invalid username/password.');
    }

    public function dashboardPage(){
        $id = session('id');
        if ($id){
            $root = TermRegistered::where([['term_name', session('term')], ['coordinator_id', session('id')]])->first();
            $term = Term::all()->last();
            session()->forget('excel');
            return view('Coordinator.dashboard', compact('root', 'term'));
        } else {
            return Redirect("/coordinator/loginForm");
        }
    }
    
    public function sendLetter(){
        $id = session('id');
        if ($id){
            $student = TermRegistered::where('term_name', session('term'))->distinct()->get();
            $root = TermRegistered::where([['term_name', session('term')], ['coordinator_id', session('id')]])->first();
            $term = Term::all()->last();
            return view('Coordinator.sendletter', compact('student', 'root', 'term'));
        } else {
            return Redirect('/coordinator/loginForm');
        }
    }

    public function letter(Request $request){
        $id = session('id');
        if ($id){
            $validated = $request->validate([
                'regno' => 'required|max:100',
            ],
            [
                'regno.required' => 'Please send letter to at least one student',
            ]);

            foreach ($request->regno as $r){
                $rendererName = Settings::PDF_RENDERER_TCPDF;
                $renderedLibraryPath = "../vendor/tecnickcom/tcpdf";
                Settings::setPdfRenderer($rendererName, $renderedLibraryPath);

                $student = Student::where('registration_no', $r)->first();
                $studentdocs = StudentDocs::where('registration_no', $r)->first();

                if($studentdocs){
                    $templateProcessor = new TemplateProcessor('templates/recLetter.docx');
                    $templateProcessor->setValue('description', $request->description);
                    $templateProcessor->setValue('registration_no', $student->registration_no);
                    $templateProcessor->setValue('name', $student->name);
                    $templateProcessor->setValue('department', $student->department);
                    $templateProcessor->setValue('contactno', $student->contactno);
                    $templateProcessor->setValue('date', $studentdocs->created_at->toFormattedDateString());
                    $templateProcessor->saveAs($student->registration_no.'.docx');

                    $objReader = PhpWord\IOFactory::createReader();
                    $pdfWord = $objReader->load($student->registration_no.'.docx');
                    $objWriter = PhpWord\IOFactory::createWriter($pdfWord, 'PDF');
                    $file = $student->registration_no.".pdf";
                    $objWriter->save($file);
                    
                    $pdffile = "files/".$file;
                    rename($file, $pdffile);
                    unlink($student->registration_no.'.docx');

                    $studentdocs->where('registration_no', $r)->update([
                        'recommendation_letter' => $pdffile,
                    ]);
                } else {
                    $templateProcessor = new TemplateProcessor('templates/recLetter.docx');
                    $templateProcessor->setValue('description', $request->description);
                    $templateProcessor->setValue('registration_no', $student->registration_no);
                    $templateProcessor->setValue('name', $student->name);
                    $templateProcessor->setValue('department', $student->department);
                    $templateProcessor->setValue('contactno', $student->contactno);
                    $templateProcessor->setValue('date', Carbon::now()->toFormattedDateString());
                    $templateProcessor->saveAs($student->registration_no.'.docx');

                    $objReader = PhpWord\IOFactory::createReader();
                    $pdfWord = $objReader->load($student->registration_no.'.docx');
                    $objWriter = PhpWord\IOFactory::createWriter($pdfWord, 'PDF');
                    $file = $student->registration_no.".pdf";
                    $objWriter->save($file);
                    
                    $pdffile = "files/".$file;
                    rename($file, $pdffile);
                    unlink($student->registration_no.'.docx');

                    $studentdocs = new StudentDocs;
                    $studentdocs->registration_no = $student->registration_no;
                    $studentdocs->recommendation_letter = $pdffile;
                    $studentdocs->save();
                }
                Mail::to($student->email)->send(new LetterMail($student, $pdffile));
            }
            return Redirect()->back()->with("message", "Recommendation letter successfully sent.");
        } else {
            return Redirect('/coordinator/loginForm');
        }
    }

    public function uploadInternshipPlan(){
        $id = session('id');
        if ($id){
            $student = TermRegistered::where('term_name', session('term'))->distinct()->get();
            $root = TermRegistered::where([['term_name', session('term')], ['coordinator_id', session('id')]])->first();
            $term = Term::all()->last();
            return view('Coordinator.orientation', compact('student', 'root', 'term'));
        } else {
            return Redirect('/coordinator/loginForm');
        }
    }

    public function internshipPlan(Request $request){
        $id = session('id');
        if($id){
            $validated = $request->validate([
                'orientation_date' => 'required',
                'orientation_venue' => 'required',
                'internshipPlan' => 'required|mimes:pdf'
            ],
            [
                'orientation_date.required' => 'Please set date.',
                'orientation_venue.required' => 'Please add information about venue.',
                'internshipPlan.required' => 'Please upload file.'
            ]);


            $term = Term::all()->last();
            $offerletterfile = $request->file("internshipPlan");
            $file_ext = strtolower($offerletterfile->getClientOriginalExtension());
            $file_name = $term->term_name."InternshipPlan.".$file_ext;
            $offerletterfile->move("files", $file_name);

            
            $term->where('term_name', $term->term_name)->update([
                'internship_plan' => 'files/'.$file_name
            ]);

            $student = Student::all();
            foreach($student as $s){
                $announcement = new Announcement;
                $announcement->registration_no = $s->registration_no;
                $announcement->purpose = "Orientation Status";
                $announcement->description = $request->orientation_venue;
                $announcement->start_date = Carbon::now();
                $announcement->end_date = $request->orientation_date;
                $announcement->coordinator_id = $id;
                $announcement->save();
            }

            return Redirect()->back();
        } else {
            return Redirect('/coordinator/loginForm');
        }
    }

    public function studentsinfo(){
        $id = session('id');
        if ($id){
            $root = TermRegistered::where([['term_name', session('term')], ['coordinator_id', session('id')]])->first();
            $student = TermRegistered::where('term_name', session('term'))->distinct()->get();
            $term = Term::all()->last();
            return view('Coordinator.studentsinfo', compact('root', 'student', 'term'));
        } else {
            return Redirect('/coordinator/loginForm');
        }
    }

    public function changePassword(){
        $id = session('id');
        if ($id){
            $root = TermRegistered::where([['term_name', session('term')], ['coordinator_id', session('id')]])->first();
            $term = Term::all()->last();
            return view('Coordinator.changepassword', compact('root', 'term'));
        } else {
            return Redirect('/coordinator/loginForm');
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

            $coordinator = Coordinator::where('id', $id)->first();

            if ($coordinator){
                if (Hash::check($request->curpassword, $coordinator->password)){
                    if ($request->newpassword == $request->confirmpassword){
                        $coordinator->password = Hash::make($request->newpassword);
                        $coordinator->save();
                        return Redirect('/coordinator/loginForm')->with("message", "Password successfully changed.");
                    } else {
                        return Redirect()->back()->with("message", "Password not matched.");
                    }
                } else {
                    return Redirect()->back()->with("message", "Incorrect current password.");
                }
            }

        } else {
            return Redirect('/coordinator/loginForm');
        }
    }

    public function logout(){
        session()->forget('id');
        return Redirect('/coordinator/loginForm');
    }

    function generate_letter_pass(){
        $random = '';
        for($i = 0; $i < 6; $i++){
            $random .= rand(0, 1) ? rand(0, 9) : chr(rand(ord('a'), ord('z')));
        }
        return $random;
    }

    public function studentlogininfo(Request $request){
        $id = session('id');
        if ($id){
            $validated = $request->validate([
                'regno' => 'required|max:100',
            ],
            [
                'regno.required' => 'Please mark at least one student login',
            ]);

            foreach ($request->regno as $r){
                $studentaccount = StudentAccount::where('registration_no', $r)->first();
                $student = Student::where('registration_no', $r)->first();
                $studentdocs = StudentDocs::where('registration_no', $r)->first();

                if($studentaccount){
                    $studentaccount->where('registration_no', $r)->update([
                        'login_id' => strtolower($student->registration_no),
                        'password' => $this->generate_letter_pass(),
                        'one_time_auth' => null
                    ]);
                } else {
                    $studentaccount = new StudentAccount;
                    $studentaccount->registration_no = $student->registration_no;
                    $studentaccount->login_id = strtolower($student->registration_no);
                    $studentaccount->password = $this->generate_letter_pass();
                    $studentaccount->one_time_auth = null;
                    $studentaccount->login_date = Carbon::now();
                    $studentaccount->save();
                }

                if(!$studentdocs){
                    $studentdocs = new StudentDocs;
                    $studentdocs->registration_no = $student->registration_no;
                    $studentdocs->save();
                }

                Mail::to($student->email)->send(new LoginInfoMail($student));
            }

            return Redirect()->back()->with("message", "Student login info successfully sent.");
        } else {
            return Redirect('/coordinator/loginForm');
        }
    }

    public function getplan(){
        $id = session('id');
        if ($id){
            $root = TermRegistered::where([['term_name', session('term')], ['coordinator_id', session('id')]])->first();
            $carbon = Carbon::now();
            $term = Term::all()->last();
            return view('Coordinator.plan', compact('root', 'carbon', 'term'));
        } else {
            return Redirect('/coordinator/loginForm');
        }
    }

    public function setplan(Request $request){
        $id = session('id');
        if ($id){
            $validated = $request->validate([
                'termapply' => 'required|max:100',
                'termoffer' => 'required|max:100',
                'termcomplete' => 'required|max:100',
                'termevaluation' => 'required|max:100'
            ],
            [
                'termapply.required' => 'Please select date',
                'termoffer.required' => 'Please select date',
                'termcomplete.required' => 'Please select date',
                'termevaluation.required' => 'Please select date'
            ]);

            $term = Term::where('term_name', session('term'))->first();
            if ($term){
                $term->where('term_name', session('term'))->update([
                    'apply_for_internship' => $request->termapply,
                    'apply_for_internship' => $request->termapply,
                    'acquisition_offer_letter' => $request->termoffer,
                    'acquisition_completion_certificate' => $request->termcomplete,
                    'final_evaluation' => $request->termevaluation
                ]);
            }
            return Redirect()->back()->with("message", "Term plan updated successfully.");
        } else {
            return Redirect('/coordinator/loginForm');
        }
    }
    
    public function changeterm(){
        $id = session('id');
        if ($id){
            $root = TermRegistered::where([['term_name', session('term')], ['coordinator_id', session('id')]])->first();
            $term = Term::all()->last();
            $terms = TermRegistered::where('coordinator_id', $id)->distinct()->get(['term_name']);
            return view('Coordinator.changeterm', compact('root', 'term', 'terms'));
        } else {
            return Redirect("/coordinator/loginForm");
        }
    }

    public function portallogin(){
        $id = session('id');
        if ($id){
            $root = TermRegistered::where([['term_name', session('term')], ['coordinator_id', session('id')]])->first();
            $term = Term::all()->last();
            $student = TermRegistered::where('term_name', session('term'))->distinct()->get();
            return view('Coordinator.portallogin', compact('root', 'term', 'student'));
        } else {
            return Redirect("/coordinator/loginForm");
        }
    }

    public function selectterm(Request $request){
        $id = session('id');
        if ($id){
            session([
                "term" => $request->termname
            ]);
            return Redirect()->back();
        } else {
            return Redirect("/coordinator/loginForm");
        }
    }

    public function uploadfile(){
        $id = session('id');
        if ($id){
            $root = TermRegistered::where([['term_name', session('term')], ['coordinator_id', session('id')]])->first();
            $term = Term::all()->last();
            $output = session('excel');
            return view('Coordinator.uploadfile', compact('output', 'root', 'term'));
        } else {
            return Redirect("/coordinator/loginForm");
        }
    }

    public function postfile(Request $request){
        $id = session('id');
        if ($id){
            $validated = $request->validate([
                'studentlist' => 'required|mimes:xlsx,xls,csv'
            ],
            [
                'studentlist.required' => 'Please upload excel file.'

            ]);
            
            $output = array();
            $file = $request->file('studentlist');
            $file_ext = strtolower($file->getClientOriginalExtension());
            $spreadsheet = PhpSpreadsheet\IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            foreach ($worksheet->getRowIterator() as $row){
                $object = array();
                $cellIterator = $row->getCellIterator();

                foreach ($cellIterator as $cell){
                    array_push($object, $cell->getValue());
                }
                array_push($output, $object);
            }
            session([
                'excel' => $output
            ]);
            return Redirect()->back();
        } else {
            return Redirect("/coordinator/loginForm");
        }
    }

    public function setAnnouncement(){
        $id = session('id');
        if ($id){
            $root = TermRegistered::where([['term_name', session('term')], ['coordinator_id', session('id')]])->first();
            $student = TermRegistered::where('term_name', session('term'))->distinct()->get();
            $term = Term::all()->last();
            return view("Coordinator.setannouncement", compact('root', 'student', 'term'));
        } else {
            return Redirect("/coordinator/loginForm");
        }
    }

    public function forgotpasswordlink(){
        return view('Coordinator.forgotpasswordlink');
    }

    public function sendforgotpasswordemail(Request $request){
        $validated = $request->validate([
            'email' => 'required|max:100',
        ],
        [
            'email.required' => 'Please enter email',
        ]);

        $coordinator = Coordinator::where('email', $request->email)->first();

        if($coordinator){
            Mail::to($coordinator->email)->send(new CoordinatorForgotPwd($coordinator));
            return Redirect()->back()->with("message", "Password request has been sent. Please check your email.");
        } else {
            return Redirect()->back()->with('message', "Sorry, you're email is not registered.");
        }
    }

    public function forgotpassword($name){
        $coordinator = Coordinator::where('name', Crypt::decryptString($name))->first();

        if($coordinator){
            return view('Coordinator.forgotpassword', compact('coordinator'));
        } else {   
            return Redirect('coordinator/loginForm');
        }
    }

    public function setforgotpassword(Request $request, $name){
        $validated = $request->validate([
            'newpassword' => 'required|max:100|string|min:6|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
            'confirmpassword' => 'required|max:100|string|min:6|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/'
        ],
        [
            'newpassword.required' => 'Please enter new password',
            'confirmpassword.required' => 'Please enter confirm password'
        ]);
        
        $coordinator = Coordinator::where('name', $name)->first();

        if($coordinator){
             if($request->newpassword == $request->confirmpassword){
                $coordinator->where('name', $name)->update([
                    'password' => Hash::make($request->confirmpassword),
                ]);
            } else {
                return Redirect()->back()->with("message", "Password not matched.");
            }
        }

        return Redirect('coordinator/loginForm')->with("message", "You're password is set successfully. Please login.");
    }

    public function getOrganizationList(){
        $id = session('id');
        if ($id){
            $root = TermRegistered::where([['term_name', session('term')], ['coordinator_id', session('id')]])->first();
            $term = Term::all()->last();
            $student = TermRegistered::where('term_name', session('term'))->distinct()->get();
            return view('Coordinator.organizationlist', compact('root', 'term', 'student'));
        } else {
            return Redirect("/coordinator/loginForm");
        }
    }

    public function announcement(Request $request){
        $id = session('id');
        if ($id){
            $validated = $request->validate([
                'purpose' => 'required',
                'users' => 'required',
                'startDate' => 'required',
                'endDate' => 'required',
                'description' => 'required'
            ]);

            foreach($request->users as $u){
                if($u == "allStudent"){
                    $student = Student::all();
                    foreach($student as $s){
                        $announcement = new Announcement;
                        $announcement->registration_no = $s->registration_no;
                        $announcement->purpose = $request->purpose;
                        $announcement->description = $request->description;
                        $announcement->start_date = $request->startDate;
                        $announcement->end_date = $request->endDate;
                        $announcement->coordinator_id = $id;
                        $announcement->save();
                        
                        if($request->announcementMethod){
                            foreach($request->announcementMethod as $a){
                                Mail::to($s->email)->send(new AnnouncementMail($request->description));
                            }
                        }
                    }
                }
            }

            return Redirect()->back()->with("message", "Announcement made successfully.");
        } else {
            return Redirect("/coordinator/loginForm");
        }
    }

    public function organizations(){
        $id = session('id');
        if ($id){
            $root = TermRegistered::where([['term_name', session('term')], ['coordinator_id', session('id')]])->first();
            $term = Term::all()->last();
            $student = TermRegistered::where('term_name', session('term'))->distinct()->get();
            $org = Organization::all();
            return view('Coordinator.organizations', compact('root', 'term', 'student', 'org'));
        } else {
            return Redirect("/coordinator/loginForm");
        }
    }

    public function supervisors($organizationNTN){
        $id = session('id');
        if ($id){
            $root = TermRegistered::where([['term_name', session('term')], ['coordinator_id', session('id')]])->first();
            $term = Term::all()->last();
            $student = TermRegistered::where('term_name', session('term'))->distinct()->get();
            $supervisor = Supervisor::where('organization_ntn_no', $organizationNTN)->get();
            return view('Coordinator.supervisors', compact('root', 'term', 'student', 'supervisor'));
        } else {
            return Redirect("/coordinator/loginForm");
        }
    }

    public function offerletter_status(Request $request, $registration_no){
        $id = session('id');
        if ($id){
            $validated = $request->validate([
                'description'=>'required'
            ],
            [
                'description.required' => 'Please enter remarks',
            ]);

            
            $term = Term::all()->last();
            $student = TermRegistered::where([['term_name', $term->term_name], ['registration_no', $registration_no]])->first();
            if ($student){
                $student->where([['term_name', $term->term_name], ['registration_no', $registration_no]])->update([
                    'offer_letter_status' => $request->status
                ]);
                $announcement = new Announcement;
                $announcement->registration_no = $registration_no;
                $announcement->purpose = "Offer Letter Status";
                $announcement->description = $request->description;
                $announcement->start_date = Carbon::now();
                $announcement->end_date = Carbon::now()->addDays(7);
                $announcement->coordinator_id = $id;
                $announcement->save();
            }
            return Redirect()->back()->with("message", "You've set student's offer letter status successfully.");
        } else {
            return Redirect("/coordinator/loginForm");
        }
    }

    public function internshipcompletion_status(Request $request, $registration_no){
        $id = session('id');
        if ($id){
            $validated = $request->validate([
                'description'=>'required'
            ],
            [
                'description.required' => 'Please enter remarks',
            ]);

            
            $term = Term::all()->last();
            $student = TermRegistered::where([['term_name', $term->term_name], ['registration_no', $registration_no]])->first();
            if ($student){
                $student->where([['term_name', $term->term_name], ['registration_no', $registration_no]])->update([
                    'document_status' => $request->status
                ]);
                $announcement = new Announcement;
                $announcement->registration_no = $registration_no;
                $announcement->purpose = "Document Status";
                $announcement->description = $request->description;
                $announcement->start_date = Carbon::now();
                $announcement->end_date = Carbon::now()->addDays(7);
                $announcement->coordinator_id = $id;
                $announcement->save();
            }
            return Redirect()->back()->with("message", "You've set student's documents status successfully.");
        } else {
            return Redirect("/coordinator/loginForm");
        }
    }

    public function internshipreport_status(Request $request, $registration_no){
        $id = session('id');
        if ($id){
            $term = Term::all()->last();
            $student = TermRegistered::where([['term_name', $term->term_name], ['registration_no', $registration_no]])->first();
            if ($student){
                $student->where([['term_name', $term->term_name], ['registration_no', $registration_no]])->update([
                    'internship_report_status' => $request->status
                ]);
                $announcement = new Announcement;
                $announcement->registration_no = $registration_no;
                $announcement->purpose = "Internship report Status";
                if ($request->status == 'approved'){
                    $announcement->description = "Congratulations, you're internship report has been accepted.";
                } else {
                    $announcement->description = "You're internship report is not accepted due to mistakes or vague information of your document. Please reupload your report to avoid such issues.";
                }
                $announcement->start_date = Carbon::now();
                $announcement->end_date = Carbon::now()->addDays(7);
                $announcement->coordinator_id = $id;
                $announcement->save();
            }
            return Redirect()->back()->with("message", "You've set student's internship report status successfully.");
        } else {
            return Redirect("/coordinator/loginForm");
        }
    }

    // public function offerletterinfo(){
    //     $id = session('id');
    //     if ($id){
    //         $root = TermRegistered::where([['term_name', session('term')], ['coordinator_id', session('id')]])->first();
    //         $student = TermRegistered::where('term_name', session('term'))->distinct()->get();
    //         $term = Term::all()->last();
    //         return view("Coordinator.setannouncement", compact('root', 'student', 'term'));
    //     } else {
    //         return Redirect("/coordinator/loginForm");
    //     }
    // }
}
