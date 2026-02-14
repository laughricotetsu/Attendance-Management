@extends('layouts.guest')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin_login.css') }}">
@endsection

@section('content')
<div class="admin-login-wrapper">
    <div class="admin-login-card">
        <div class="admin-login-title">管理者ログイン</div>

        <form method="POST" action="{{ route('admin.login.post') }}" class="admin-login-form">
            @csrf

            <div>
                <label>メールアドレス</label>
                <input type="email" name="email">
            </div>

            <div>
                <label>パスワード</label>
                <input type="password" name="password">
            </div>

            <button type="submit" class="admin-login-button">
                管理者ログインする
            </button>
        </form>
    </div>
</div>
@endsection
