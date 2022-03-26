<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InternConfirm;
use App\Mail\StudentConfirmationMail;
use Illuminate\Support\Facades\Mail;

use Carbon\Carbon;

class ConfirmationController extends Controller
{
    public function studentConfirm($link){
        $internconfirm = InternConfirm::where('link', $link)->first();
        if($internconfirm){
            if ($internconfirm->expire_date > Carbon::now()){
                if ($internconfirm->status == 'authenticated'){
                    return "This link is already authenticated. You can close this window.";
                } else {
                    $internconfirm->where('link', $link)->update([
                        'status'=>'authenticated',
                    ]);
                    Mail::to($internconfirm->students->email)->send(new StudentConfirmationMail($internconfirm->students));
                    return "Thank you for your cooperation. You can close this window.";
                }
            } else {
                return "Sorry, this link is expired. You can close this window.";
            }
        } else {
            return "Sorry, this link is expired. You can close this window.";
        }
    }
}