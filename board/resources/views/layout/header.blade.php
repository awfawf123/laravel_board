<h2>header</h2>
{{-- 로그인상태 --}}
@auth
    <div><a href="{{route('users.logout')}}">Logout</a></div>
    <div><a href="{{route('users.update')}}">회원정보변경</a></div>
@endauth
{{-- 비로그인상태 --}}
@guest
    <div><a href="{{route('users.login')}}">Login</a></div>
@endguest
<hr>