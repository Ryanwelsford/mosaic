@extends('head')

@section('title', $title)

<body class="login">
    <div class="login-box">
        <div class="fill-top center-column"><h1>Login</h1></div>
        <form class="center-column align-center" action="{{ Route('login') }}" method="POST">
            @csrf
            <div class="center-column login-holder">
                <label>Email @error('email') <span class="error-text">*</span> @enderror</label>
                <input type="text" name="email" value="@if(isset($user->email)) {{ $user->email }} @endif">
                @error('email')
                    <div class="small-error-text error-text">{{ $message }} </div>
                @enderror
            </div>
            <div class="center-column login-holder margin-top-10">
                <label>Password @error('email') <span class="error-text">*</span> @enderror</label>
                <input type="text" name="password">
                @error('password')
                    <div class="small-error-text error-text">{{ $message }} </div>
                @enderror
            </div>

            <div class="row-space-between margin-top-10">
                Stay Signed in
                <label class="ph-checkbox-label">
                    <input class="ph-checkbox" type="checkbox" name="" value ="" >
                    <span class="checkmark"></span>
                </label>
            </div>

            <div class="error-text">
                @if(session("loginError"))
                    {{ session("loginError") }}
                @endif
            </div>

            <div>
                @if(session("logout"))
                    {{ session("logout") }}
                @endif
            </div>
            <button type="submit" class="ph-button ph-button-standard ph-button-large margin-top-10">Login</button>
        </form>
        <div class="center-column" ><img class="logo" src="/images/phr-logo.svg"></div>
    </div>
</body>
