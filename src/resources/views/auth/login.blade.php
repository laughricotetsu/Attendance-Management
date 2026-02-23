@extends('layouts.guest')

@section('title', 'ログイン')

@section('css')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('content')
<div class="login-wrapper">
    <div class="login-box">

        <h1 class="login-title">ログイン</h1>

        <form method="POST" action="{{ route('login') }}" novalidate>
            @csrf

            <div class="form-group">
                <label>メールアドレス</label>
                <input type="email" name="email" required>
                @error('email')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label>パスワード</label>
                <input type="password" name="password" required>
                @error('password')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="login-button">
                ログインする
            </button>

            <div class="register-link">
                <a href="{{ route('register') }}">会員登録はこちら</a>
            </div>

        </form>
    </div>
</div>
@endsection
