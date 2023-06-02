<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\MailNotify;
use Exception;

class MailController extends Controller
{
    public function index(){
        $data = [
            'subject' => 'Test email'
            ,'body' =>'Hello'
        ];
        try{
            Mail::to('awfawf123@naver.com')->send(new MailNotify($data));
            return response()->json(['Great check your email']);

        }catch(Exception $e){
            return response()->json(['Sorry check your email']);
        }
    }
}
