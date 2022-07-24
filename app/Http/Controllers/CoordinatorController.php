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
use App\Models\VivaLink;
use Illuminate\Support\Facades\Crypt;
use App\Mail\LetterMail;
use App\Mail\LoginInfoMail;
use App\Mail\AnnouncementMail;
use App\Mail\OrientationMail;
use App\Mail\ResponseMail;
use App\Mail\VivaMail;
use App\Mail\StudentViva;
use App\Mail\GradeMail;
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
        $id = session('id');
        if($id){
            return Redirect('/coordinator/dashboard');
        } else {
            return view('Coordinator.login');
        }
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

        if ($coordinator && $coordinator->password){
            $password = Crypt::decryptString($coordinator->password);
            if ($request->password == $password){
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
            $student = TermRegistered::where('term_name', session('term'))->distinct()->orderBy('registration_no', 'asc')->get();
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
                $rendererName = Settings::PDF_RENDERER_DOMPDF;
                $renderedLibraryPath = "../vendor/dompdf/dompdf";
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
                    // $term = Term::all()->last();/*To be removed afterwards*/
                    // $file = $term->term_name.'-'.$student->registration_no.".docx";/*To be removed afterwards*/
                    $templateProcessor->saveAs($student->registration_no.'.docx');

                    $objReader = PhpWord\IOFactory::createReader();
                    $pdfWord = $objReader->load($student->registration_no.'.docx');
                    $objWriter = PhpWord\IOFactory::createWriter($pdfWord, 'PDF');
                    $term = Term::all()->last();
                    $file = $term->term_name.'-'.$student->registration_no.".pdf";
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
                    // $term = Term::all()->last();/*To be removed afterwards*/
                    // $file = $term->term_name.'-'.$student->registration_no.".docx";/*To be removed afterwards*/
                    $templateProcessor->saveAs($student->registration_no.'.docx');

                    $objReader = PhpWord\IOFactory::createReader();
                    $pdfWord = $objReader->load($student->registration_no.'.docx');
                    $objWriter = PhpWord\IOFactory::createWriter($pdfWord, 'PDF');
                    $term = Term::all()->last();
                    $file = $term->term_name.'-'.$student->registration_no.".pdf";
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
            $student = TermRegistered::where('term_name', session('term'))->distinct()->orderBy('registration_no', 'asc')->get();
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

                Mail::to($s->email)->send(new OrientationMail($s, $request->orientation_venue, $request->orientation_date, "files/".$file_name));
            }

            return Redirect()->back()->with("message", "Orientation message successfully sent.");
        } else {
            return Redirect('/coordinator/loginForm');
        }
    }

    public function studentsinfo(){
        $id = session('id');
        if ($id){
            $root = TermRegistered::where([['term_name', session('term')], ['coordinator_id', session('id')]])->first();
            $student = TermRegistered::where('term_name', session('term'))->distinct()->orderBy('registration_no', 'asc')->get();
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
                $password = Crypt::decryptString($coordinator->password);
                if ($request->curpassword == $password){
                    if ($request->newpassword == $request->confirmpassword){
                        $coordinator->password = Crypt::encryptString($request->newpassword);
                        $coordinator->save();
                        session()->forget('id');
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

    public function studentloginfileinfo(Request $request){
        $id = session('id');
        if ($id){
            $output = session("excel");

            if(!$output){
                return Redirect()->back()->with("message", "File must be uploaded first.");
            }

            for ($x = 1; $x < count($output); $x++){
                $student = Student::where('registration_no', $output[$x][0])->first();

                if(!$student){
                    $student = new Student;
                    $student->registration_no = $output[$x][0];
                    $student->name = $output[$x][1];
                    $student->email = $output[$x][2];
                    $student->CGPA = $output[$x][3];
                    $student->cr_hrs = $output[$x][4];
                    $student->contact_no = $output[$x][5];
                    $student->department = $output[$x][6];
                    $student->save();

                    $termRegistered = new TermRegistered;
                    $termRegistered->registration_no = $output[$x][0];
                    $termRegistered->term_name = session('term');
                    $termRegistered->coordinator_id = $id;
                    $termRegistered->save();

                    $studentaccount = new StudentAccount;
                    $studentaccount->registration_no = $output[$x][0];
                    $studentaccount->login_id = strtolower($output[$x][0]);
                    $studentaccount->password = $this->generate_letter_pass();
                    $studentaccount->one_time_auth = null;
                    $studentaccount->save();

                    $studentdocs = new StudentDocs;
                    $studentdocs->registration_no = $output[$x][0];
                    $studentdocs->save();

                    Mail::to($student->email)->send(new LoginInfoMail($student));
                }
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
            ],
            [
                'termapply.required' => 'Please select date',
                'termoffer.required' => 'Please select date',
                'termcomplete.required' => 'Please select date',
            ]);

            $term = Term::where('term_name', session('term'))->first();
            if ($term){
                $term->where('term_name', session('term'))->update([
                    'apply_for_internship' => $request->termapply,
                    'upload_offer_letter_date' => $request->termoffer,
                    'upload_document_date' => $request->termcomplete,
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
            $student = TermRegistered::where('term_name', session('term'))->distinct()->orderBy('registration_no', 'asc')->get();
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
            $student = TermRegistered::where('term_name', session('term'))->distinct()->orderBy('registration_no', 'asc')->get();
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
                $password = Crypt::encryptString($request->confirmpassword);
                $coordinator->where('name', $name)->update([
                    'password' => $password,
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
            $student = TermRegistered::where('term_name', session('term'))->distinct()->orderBy('registration_no', 'asc')->get();
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
            $student = TermRegistered::where('term_name', session('term'))->distinct()->orderBy('registration_no', 'asc')->get();
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
            $student = TermRegistered::where('term_name', session('term'))->distinct()->orderBy('registration_no', 'asc')->get();
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
                'description'=>'required',
                'status' => 'required'
            ],
            [
                'description.required' => 'Please enter remarks',
                'status.required' => 'Please select status'
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

                Mail::to($student->students->email)->send(new ResponseMail($student->students, $request->description));
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
                'description'=>'required',
                'status' => 'required'
            ],
            [
                'description.required' => 'Please enter remarks',
                'status.required' => 'Please select status'
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

                Mail::to($student->students->email)->send(new ResponseMail($student->students, $request->description));
            }
            return Redirect()->back()->with("message", "You've set student's documents status successfully.");
        } else {
            return Redirect("/coordinator/loginForm");
        }
    }

    public function selectViva($registrationno){
        $id = session('id');
        if ($id){
            $root = TermRegistered::where([['term_name', session('term')], ['coordinator_id', session('id')]])->first();
            $term = Term::all()->last();
            $student = TermRegistered::where([['term_name', session('term')], ['registration_no', $registrationno]])->first();
            $coordinator = Coordinator::all()->except($id);
            return view('Coordinator.selectviva', compact('root', 'term', 'student', 'coordinator'));
        } else {
            return Redirect("/coordinator/loginForm");
        }
    }

    public function viva(Request $request, $registrationno, $term){
        $id = session('id');
        if ($id){
            $validated = $request->validate([
                'Date'=>'required'
            ],
            [
                'Date.required' => 'Please select date',
            ]);

            $link = VivaLink::where('registration_no', $registrationno)->first();
            if($link){
                $link->where('registration_no', $registrationno)->update([
                    'link' => Crypt::encryptString($registrationno),
                    'date'=> $request->Date,
                ]);
            } else {
                $link = new VivaLink;
                $link->registration_no = $registrationno;
                $link->link = Crypt::encryptString($registrationno);
                $link->date = $request->Date;
                $link->save();
            }
            $student = TermRegistered::where([['term_name', $term], ['registration_no', $registrationno]])->first();
            $student->update([
                'evaluator_id' => $request->viva,
            ]);

            $announcement = new Announcement;
            $announcement->registration_no = $registrationno;
            $announcement->purpose = "Viva Status";
            $announcement->description = "You're Viva is scheduled on ".Carbon::parse($request->Date)->toFormattedDateString()." and it will be taken by ".$student->evaluators->name.".";
            $announcement->start_date = Carbon::now();
            $announcement->end_date = Carbon::now()->addDays(7);
            $announcement->coordinator_id = $id;
            $announcement->save();

            Mail::to($student->evaluators->email)->send(new VivaMail($student->students, Crypt::encryptString($registrationno), $student->evaluators, $term));
            Mail::to($student->students->email)->send(new StudentViva($student->students, $student->evaluators, $term));
            return Redirect()->back()->with('message', 'Email sent successfully.');
        } else {
            return Redirect("/coordinator/loginForm");
        }
    }

    public function startviva($term, $link){
        $viva_link = VivaLink::where('registration_no', Crypt::decryptString($link))->first();
        if($viva_link){
            $root = TermRegistered::where([['term_name', $term], ['registration_no', $viva_link->registration_no]])->first();
            return view('Coordinator.startviva', compact('root'));
        } else {
            return "sorry, this link is expired.";
        }
    }

    public function setGrades(Request $request, $registrationno, $term){
        $validated = $request->validate([
            'grade'=>'required',
            'description' => 'required'
        ],
        [
            'grade.required' => 'Please select grade',
            'description.required' => 'Please enter description',
        ]);

        $root = TermRegistered::where([['term_name', $term], ['registration_no', $registrationno]])->first();
        Mail::to($root->coordinators->email)->send(new GradeMail($root, $request->grade, $request->description));

        if($request->grade == "deferred"){
            $root->update([
                'evaluator_id' => null
            ]);
        } else {
            $root->update([
                'remarks' => $request->description,
                'grade' => $request->grade
            ]);
        }
        $viva_link = VivaLink::where('registration_no', $registrationno)->first();
        $viva_link->where('registration_no', $registrationno)->delete();
        return "Thank you for your cooperation. Please close this window immediately.";
    }

    public function grades(){
        $id = session('id');
        if ($id){
            $root = TermRegistered::where([['term_name', session('term')], ['coordinator_id', $id]])->first();
            $term = Term::all()->last();
            $student = TermRegistered::where('term_name', session('term'))->distinct()->orderBy('registration_no', 'asc')->get();
            return view('Coordinator.grades', compact('root', 'student', 'term'));
        } else {
            return Redirect("/coordinator/loginForm");
        }
    }

    public function gradeReport(){
        $id = session('id');
        if ($id){
            $spreadsheet = new PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $student = TermRegistered::where('term_name', session('term'))->distinct()->orderBy('registration_no', 'asc')->get();
            $sheet->setCellValue('A1', '#');
            $sheet->setCellValue('B1', 'Registration No');
            $sheet->setCellValue('C1', 'Name');
            $sheet->setCellValue('D1', 'Grade');
            $i = 2;

            foreach($student as $s){
                $sheet->setCellValue('A'.$i, $i-1);
                $sheet->setCellValue('B'.$i, $s->registration_no);
                $sheet->setCellValue('C'.$i, $s->students->name);
                if (isset($s->grade)){
                    $sheet->setCellValue('D'.$i, $s->grade);
                } else {
                    $sheet->setCellValue('D'.$i, 'Not Assigned');
                }
                $i++;
            }

            $writer = new PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('gradeReport.xlsx');
            return response()->download('gradeReport.xlsx')->deleteFileAfterSend(true);
        } else {
            return Redirect("/coordinator/loginForm");
        }
    }

    public function singleGradeReport($registration_no){
        $id = session('id');
        if ($id){
            // $rendererName = Settings::PDF_RENDERER_DOMPDF;
            // $renderedLibraryPath = "../vendor/dompdf/dompdf";
            // Settings::setPdfRenderer($rendererName, $renderedLibraryPath);

            $student = TermRegistered::where([['term_name', session('term')], ['registration_no', $registration_no]])->first();
            if ($student){
                $templateProcessor = new TemplateProcessor('templates/singleGradeReport.docx');
                $templateProcessor->setValue('student_name', $student->students->name);
                $templateProcessor->setValue('registration_no', $student->registration_no);
                $templateProcessor->setValue('term_name', $student->term_name);
                $templateProcessor->setValue('grade', $student->grade);
                $templateProcessor->setValue('c_name', $student->coordinators->name);
                $templateProcessor->setValue('designation', $student->coordinators->designation);
                $templateProcessor->setValue('date', Carbon::now()->toFormattedDateString());
                $templateProcessor->saveAs($student->registration_no.'.docx');

                // $objReader = PhpWord\IOFactory::createReader();
                // $pdfWord = $objReader->load($student->registration_no.'.docx');
                // $objWriter = PhpWord\IOFactory::createWriter($pdfWord, 'PDF');
                // $file = $student->registration_no.".pdf";
                // $objWriter->save($file);
                // unlink($student->registration_no.'.docx');
                return response()->download($student->registration_no.'.docx')->deleteFileAfterSend(true);
            } else {
                return "Sorry! student not found.";
            }
        } else {
            return Redirect("/coordinator/loginForm");
        }
    }

    public function AddStudent(){
        $id = session('id');
        if ($id){
            $root = TermRegistered::where([['term_name', session('term')], ['coordinator_id', $id]])->first();
            $term = Term::all()->last();
            $student = TermRegistered::where('term_name', session('term'))->distinct()->orderBy('registration_no', 'asc')->get();
            return view('Coordinator.addstudent', compact('root', 'student', 'term'));
        } else {
            return Redirect("/coordinator/loginForm");
        }
    }

    public function postStudent(Request $request){
        $id = session('id');
        if ($id){
            $validated = $request->validate([
                'registration_no'=>'required|regex:/BCS[0-9]{6}/',
                'student_name'=>'required',
                'student_email'=>'required',
                'student_CGPA'=>'required|numeric|between:0,99.99',
                'student_chrs'=>'required',
                'student_contactno'=>'required|numeric|digits:11',
                'student_department'=>'required'
            ],
            [
                'registration_no.required'=> 'Please enter registration no',
                'registration_no.regex'=> 'This is not valid registration no',
                'student_name.required'=> 'Please enter student name',
                'student_email.required'=> 'Please enter student email',
                'student_CGPA.required'=> 'Please enter student CGPA',
                'student_chrs.required'=> 'Please enter student chrs',
                'student_contactno.required'=> 'Please enter student contact no',
                'student_department.required'=> 'Please enter student department'
            ]);

            $student = Student::where('registration_no', $request->registration_no)->first();
            if ($student){
                return Redirect()->back()->with('message', "This student is already on this term. You can check it from 'fetch from portal' option in the dashboard menu under 'generate login' OR you can check it in 'show students info'.");
            } else {
                $student = new Student;
                $student->registration_no = $request->registration_no;
                $student->name = $request->student_name;
                $student->email = $request->student_email;
                $student->CGPA = $request->student_CGPA;
                $student->cr_hrs = $request->student_chrs;
                $student->contact_no = $request->student_contactno;
                $student->department = $request->student_department;
                $student->save();

                $student = Student::where('registration_no', $request->registration_no)->first();
                $termRegistered = new TermRegistered;
                $termRegistered->registration_no = $student->registration_no;
                $termRegistered->term_name = session('term');
                $termRegistered->coordinator_id = $id;
                $termRegistered->save();

                $studentaccount = new StudentAccount;
                $studentaccount->registration_no = $student->registration_no;
                $studentaccount->login_id = strtolower($student->registration_no);
                $studentaccount->password = $this->generate_letter_pass();
                $studentaccount->one_time_auth = null;
                $studentaccount->save();

                $studentdocs = new StudentDocs;
                $studentdocs->registration_no = $student->registration_no;
                $studentdocs->save();

                Mail::to($student->email)->send(new LoginInfoMail($student));
            }

            return Redirect()->back()->with('message', "Student added successfully and email has been sent to student.");
        } else {
            return Redirect("/coordinator/loginForm");
        }
    }
}
