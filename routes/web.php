<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CoordinatorController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ConfirmationController;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


/*********************************** Coordinator Routes ***********************************/

Route::get('/coordinator/loginForm', [CoordinatorController::class, 'loginForm']);
Route::get('/coordinator/dashboard', [CoordinatorController::class, 'dashboardPage']);
Route::get('/coordinator/sendletter', [CoordinatorController::class, 'sendletter']);
Route::get('/coordinator/changepassword', [CoordinatorController::class, 'changePassword']);
Route::get('/coordinator/logout', [CoordinatorController::class, 'logout']);
Route::get('/coordinator/studentsinfo', [CoordinatorController::class, 'studentsinfo']);
Route::get('/coordinator/getplan', [CoordinatorController::class, 'getplan']);
Route::get('/coordinator/getplan', [CoordinatorController::class, 'getplan']);
Route::get('/coordinator/changeterm', [CoordinatorController::class, 'changeterm']);
Route::get('/coordinator/portallogin', [CoordinatorController::class, 'portallogin']);
Route::get('/coordinator/uploadfile', [CoordinatorController::class, 'uploadfile']);
Route::get('/coordinator/forgotpasswordlink', [CoordinatorController::class, 'forgotpasswordlink']);
Route::get('/coordinator/setannouncement', [CoordinatorController::class, 'setAnnouncement']);
Route::get('/coordinator/forgotpassword/{name}', [CoordinatorController::class, 'forgotpassword']);
Route::get('/coordinator/organizationlist', [CoordinatorController::class, 'getOrganizationList']);
Route::get('/coordinator/organizations', [CoordinatorController::class, 'organizations']);
Route::get('/coordinator/{organizationNTN}/supervisors', [CoordinatorController::class, 'supervisors']);

Route::post('/coordinator/login', [CoordinatorController::class, 'login'])->name("validateCoordinator");
Route::post('/coordinator/password', [CoordinatorController::class, 'password'])->name('password');
Route::post('/coordinator/letter', [CoordinatorController::class, 'letter'])->name('letter');
Route::post('/coordinator/studentlogininfo', [CoordinatorController::class, 'studentlogininfo'])->name('studentlogininfo');
Route::post('/coordinator/specificstudentlogininfo', [CoordinatorController::class, 'specificstudentlogininfo'])->name('specificstudentlogininfo');
Route::post('/coordinator/setplan', [CoordinatorController::class, 'setplan'])->name('setplan');
Route::post('/coordinator/selectterm', [CoordinatorController::class, 'selectterm'])->name('selectterm');
Route::post('/coordinator/postfile', [CoordinatorController::class, 'postfile'])->name('postfile');
Route::post('/coordinator/sendforgotpasswordemail', [CoordinatorController::class, 'sendforgotpasswordemail'])->name('coordinatorsendforgotpasswordemail');
Route::post('/coordinator/setforgotpassword/{name}', [CoordinatorController::class, 'setforgotpassword']);
Route::post('/coordinator/announcement', [CoordinatorController::class, 'announcement'])->name('announcement');

/*********************************** End of Coordinator Routes ***********************************/


/*********************************** Student Routes ***********************************/

Route::get('/student/loginForm', [StudentController::class, 'loginForm']);
Route::get('/student/dashboard', [StudentController::class, 'dashboardPage']);
Route::get('/student/logout', [StudentController::class, 'logout']);
Route::get('/student/getplan', [StudentController::class, 'getplan']);
Route::get('/student/accountsettings', [StudentController::class, 'accountsettings']);
Route::get('/student/forgotpasswordlink', [StudentController::class, 'forgotpasswordlink']);
Route::get('/student/forgotpassword/{registrationno}', [StudentController::class, 'forgotpassword']);
Route::get('/student/internshipinfo', [StudentController::class, 'internshipinfo']);
Route::get('/student/uploadofferletter', [StudentController::class, 'uploadofferletter']);
Route::get('/student/uploadinternshipreport', [StudentController::class, 'uploadinternshipreport']);
Route::get('/student/uploadcompletioncertificate', [StudentController::class, 'uploadcompletioncertificate']);

Route::post('/student/login', [StudentController::class, 'login'])->name("validateStudent");
Route::post('/student/setloginid', [StudentController::class, 'setloginid'])->name("setloginid");
Route::post('/student/setpassword', [StudentController::class, 'setpassword'])->name("setpassword");
Route::post('/student/downloadrecletter', [StudentController::class, 'downloadrecletter'])->name('recletter');
Route::post('/student/downloadinternshipplan', [StudentController::class, 'downloadinternshipplan'])->name('internshipplan');
Route::post('/student/sendforgotpasswordemail', [StudentController::class, 'sendforgotpasswordemail'])->name('sendforgotpasswordemail');
Route::post('/student/setforgotpassword/{registrationno}', [StudentController::class, 'setforgotpassword']);
Route::post('/student/setorganizationdetails', [StudentController::class, 'setOrganizationDetails'])->name("setorganizationdetails");
Route::post('/student/offerletter/{regno}', [StudentController::class, 'offerletter']);
Route::post('/student/internshipreport/{regno}', [StudentController::class, 'internshipreport']);
Route::post('/student/internshipcompletioncertificate/{regno}', [StudentController::class, 'internshipcompletioncertificate']);

/*********************************** End of Student Routes ***********************************/

/*********************************** Confirmation Routes ***********************************/

Route::get('/confirmation/{link}', [ConfirmationController::class, 'studentConfirm']);

/*********************************** End of Confirmation Routes ***********************************/

/*********************************** Administrator Routes ***********************************/

Route::get('/admin/login', [AdminController::class, 'login']);
Route::get('/admin/registration', [AdminController::class, 'registration']);
Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);
Route::get('/admin/changepassword', [AdminController::class, 'changePassword']);
Route::get('/admin/coordinator', [AdminController::class, 'coordinator']);
Route::get('/admin/postcoordinator', [AdminController::class, 'postCoordinator']);
Route::get('/admin/updatecoordinator/{coordinatorid}', [AdminController::class, 'updateCoordinator']);
Route::get('/admin/deletecoordinator/{coordinatorid}', [AdminController::class, 'deleteCoordinator']);
Route::get('/admin/logout', [AdminController::class, 'logout']);

Route::post('/admin/register', [AdminController::class, 'register'])->name('createAdmin');
Route::post('/admin/validation', [AdminController::class, 'validation'])->name('validation');
Route::post('/admin/password', [AdminController::class, 'password'])->name('adminpassword');
Route::post('/admin/addcoordinator', [AdminController::class, 'addCoordinator'])->name('addCoordinator');
Route::post('/admin/changecoordinator', [AdminController::class, 'changeCoordinator'])->name('changeCoordinator');
Route::post('/admin/removecoordinator/{coordinatorid}', [AdminController::class, 'removeCoordinator']);
