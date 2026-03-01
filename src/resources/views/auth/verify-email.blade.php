@extends('layouts.guest')

@section('content')
<div style="max-width: 600px; margin: 100px auto; text-align: center;">
    <h2>メール認証</h2>

    <p>
        登録していただいたメールアドレスに認証メールを送信しました。<br>
        メール認証を完了してください。
    </p>

    @if (session('status') == 'verification-link-sent')
        <p style="color: green;">
            認証メールを再送しました。
        </p>
    @endif

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" style="padding: 10px 20px;">
            認証メールを再送する
        </button>
    </form>
</div>
@endsection