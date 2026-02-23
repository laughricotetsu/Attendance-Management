@extends('layouts.guest')

@section('css')
<link rel="stylesheet" href="{{ asset('css/register.css') }}">
@endsection

@section('content')
<div class="auth-wrapper">

    <div class="auth-card">
        <h2 class="auth-title">会員登録</h2>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="form-group">
                <label>名前</label>
                <input type="text" name="name" value="{{ old('name') }}">
                @error('name')
                <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label>メールアドレス</label>
                <input type="email" name="email" value="{{ old('email') }}">
                @error('email')
                <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label>パスワード</label>
                <input type="password" name="password" value="{{ old('password') }}">
                @error('password')
                <div class="error-message">{{ $message }}
                </div>
                @enderror
            </div>

            <div class="form-group">
                <label>パスワード確認</label>
                <input type="password" name="password_confirmation">
            </div>

            <button type="submit" class="auth-button">
                登録する
            </button>

            <div class="auth-link">
                <a href="{{ route('login') }}">ログインはこちら</a>
            </div>

        </form>
    </div>
</div>
@endsection
