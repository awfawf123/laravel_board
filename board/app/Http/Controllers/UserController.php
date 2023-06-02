<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Password;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    function login(){
        $arr['key'] ='test';
        $arr['kim'] ='park';
        Log::emergency('emergency',$arr);
        Log::alert('alert',$arr);
        Log::critical('critical',$arr);
        Log::error('error',$arr);
        Log::warning('warning',$arr);
        Log::notice('notice',$arr);
        Log::info('info',$arr);
        Log::debug('debug',$arr);

        return view('login');
    }

    function loginpost(Request $req){

        Log::debug('로그인 시작');

        $req->validate([
            'email' => 'required|email|max:100'
            ,'password' => 'required|regex:/^(?=.*[a-zA-Z])(?=.*[!@#$%^*-])(?=.*[0-9]).{8,20}$/'
        ]);

        Log::debug('유효성 ok');

        // 유저정보 습득
        $user = User::where('email',$req->email)->first(); // first 한행만 가져옴
        if(!$user || !(Hash::check($req->password,$user->password))){
            // (형식 맞춰서) 이메일이 일치하지않거나 비밀번호 일치 하지 않을때
            $error = '아이디와 비밀번호를 확인해 주세요.';
            return redirect()->back()->with('error',$error);
        }
        // 유저 인증작업
        Auth::login($user);
        if(Auth::check()){ //true,false
            session($user->only('id')); //세션에 인증된 회원 pk 등록
            return redirect()->intended(route('boards.index'));
        }else{
            $error = '인증작업 에러';
            return redirect()->back()->with('error',$error);
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
            $error = '시스템 에러가 발생하여, 회원가입에 실패했습니다.<br>잠시 후에 다시 회원가입을 시도 해주십시오'; 
            return redirect()->route('users.registration')->with('error',$error);
        }
        // 회원가입 완료 로그인 페이지로 이동
        return redirect()->route('users.login')->with('success','회원가입을 완료 했습니다.가입하신 아이디와 비밀번호로 로그인 해 주십시오.');
    }

    function logout(){
        Session::flush(); //세션 파기
        Auth::logout(); //로그아웃
        return redirect()->route('users.login');
    }
    // 회원 탈퇴
    function withdraw(){
        $id = session('id');
        $result = User::destroy($id); // 탈퇴시 완료 메세지, 에러시 에러처리
        Session::flush(); // 세션파기
        Auth::logout();// 로그아웃
        return redirect()->route('users.login');
    }

    function update(){
        $id = session('id');
        $user = User::find($id);
        return view('update')->with('data',$user);
    }

    function updatePost(Request $req){
        $id = session('id');
        $arr = ['id' => $id];
        $req->request->add($arr);
        if(!empty($req->password)){
            $req->validate([
                'name' => 'required|regex:/^[가-힣]+$/|min:2|max:30'
                ,'email' => 'required|email|max:100'
                ,'password' => 'required_with:passwordchk|same:passwordchk|regex:/^(?=.*[a-zA-Z])(?=.*[!@#$%^*-])(?=.*[0-9]).{8,20}$/'
                ,'id' => 'required | integer'
            ]);
        }
            $req->validate([
                'name' => 'required|regex:/^[가-힣]+$/|min:2|max:30'
                ,'email' => 'required|email|max:100'
                ,'id' => 'required | integer'
            ]);
        
        $user = User::where('email',$req->email)->first(); // first 한행만 가져옴
        if($user){
            $error = '중복된 이메일 입니다.';
            return redirect()->back()->with('error',$error);
        }

        $result = User::find($id);
        $result->name = $req->name;
        $result->email = $req->email;
        if(!empty($req->password)){
            $result->password = Hash::make($req->password);
        }
        $result->save();
        
        return redirect('/boards');

        //  $arrKey = [];

        //  //유효성 체크를 하는 모든 항목 리스트
        
        // $baseUser = User::find(Auth::User()->id); //기존 데이터 획득

        // 기존 패스워드 체크
        // if(!Hash::check($req->password,$baseUser->password)){
        //     redirect()->back()->with('error','기존 비밀번호를 확인해 주세요');
        // }
        // //수정할 항목을 배열에 담는 처리
        // if($req->name !== $baseUser->name){
        //     $arrKey[] = 'name';
        // }
        // if($req->email !== $baseUser->email){
        //     $arrKey[] = 'email';
        // }
        // if(isset($req->password)){
        //     $arrKey[] = 'password';
        // }

        // $chkList = [
        //     'name' => 'required|regex:/^[가-힣]+$/|min:2|max:30'
        //     ,'email' => 'required|email|max:100'
        //     ,'bpassword' => 'regex:/^(?=.*[a-zA-Z])(?=.*[!@#$%^*-])(?=.*[0-9]).{8,20}$/'
        //     ,'password' => 'same:passwordchk|regex:/^(?=.*[a-zA-Z])(?=.*[!@#$%^*-])(?=.*[0-9]).{8,20}$/'
        // ];

        // //유효성 체크할 항목 셋팅하는 처리
        // $arrchk['bpassword'] = $chkList['bpassword'];
        // foreach ($arrKey as $val) {
        //     $arrChk[$val] = $chkList[$val];
        // }

        // $req->validate($arrchk);

        // foreach ($arrKey as $val) {
                // if($val === 'password'){
                //     $baseUser->$val = Hash::make($req->$val);
                //     continue;
                // }
        //     $baseUser->$val = $req->$val;
        // }
        // $baseUser->save();
    }
}
