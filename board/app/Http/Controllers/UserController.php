<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class UserController extends Controller
{
    function login(){
        return view('login');
    }

    function loginpost(Request $req){
        $req->validate([
            'email' => 'required|email|max:100'
            ,'password' => 'required|regex:/^(?=.*[a-zA-Z])(?=.*[!@#$%^*-])(?=.*[0-9]).{8,20}$/'
        ]);
        // 유저정보 습득
        $user = User::where('email',$req->email)->first(); // first 한행만 가져옴
        if(!$user || !(Hash::check($req->password,$user->password))){
            // 실패하거나 비밀번호 일치 하지 않을때
            $errors[] = '아이디와 비밀번호를 확인해 주세요.';
            return redirect()->back()->with('errors',collect($errors));
        }
        // 유저 인증작업
        Auth::login($user);
        if(Auth::check()){ //true,false
            session([$user->only('id')]); //세션에 인증된 회원 pk 등록
            return redirect()->intended(route('boards.index'));
        }else{
            $errors[] = '인증작업 에러';
            return redirect()->back()->with('errors',collect($errors));
        }

    }

    function registration(){
        return view('registration');
    }
    function registrationpost(Request $req){
        // 유효성 체크
        $req->validate([
            'name' => 'required|regex:/^[가-힣]+$/|min:2|max:30'
            ,'email' => 'required|email|max:100'
            ,'password' => 'required_with:passwordchk|same:passwordchk|regex:/^(?=.*[a-zA-Z])(?=.*[!@#$%^*-])(?=.*[0-9]).{8,20}$/' //required_with same : 비교해서 똑같은지 확인, 안똑같으면 에러발생
        ]);

        $data['name'] = $req->name;
        $data['email'] = $req->email;
        $data['password'] = Hash::make($req->password); //Hash 암호화

        $user = User::create($data); // insert
        if(!$user){
            $errors[] = '시스템 에러가 발생하여, 회원가입에 실패했습니다.'; 
            $errors[] = '잠시 후에 다시 회원가입을 시도 해주십시오';
            return redirect()->route('users.registration')->with('errors',collect($errors)); //errors는 일반 배열이라 collect로 변환
        }
        // 회원가입 완료 로그인 페이지로 이동
        return redirect()->route('users.login')->with('success','회원가입을 완료 했습니다.<br> 가입하신 아이디와 비밀번호로 로그인 해 주십시오.');
    }
}
