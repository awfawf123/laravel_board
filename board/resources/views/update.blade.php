@extends('layout.layout')

@section('title','Update')

@section('contents')
<h1>회원정보변경</h1>
@include('layout.errorsvalidate')
    <form action="{{route('users.update.post',['user' => $data->id])}}" method="post">
        @csrf
        <label for="name">name : </label>
        <input type="text" name="name" id="name" value="{{count($errors)>0 ? old('name') : $data->name}}">
        <label for="email">Email : </label>
        <input type="text" name="email" id="email" value="{{count($errors)>0 ? old('email') : $data->email}}">
        <label for="password">새 비밀번호 : </label>
        <input type="password" name="password" id="password">
        <label for="passwordchk">비밀번호 확인 : </label>
        <input type="password" name="passwordchk" id="passwordchk">
        <br><br>
        <button type="submit">변경</button>
        <button type="button" onclick="location.href='{{route('boards.index')}}'">Cancel</button>
    </form>
@endsection