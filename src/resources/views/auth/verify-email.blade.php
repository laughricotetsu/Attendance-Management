@extends('layouts.guest')

@section('content')

<div class="verify-bg">

    <div class="verify-box">

        <!-- <div class="verify-header">
            COACHTECH
        </div> -->

        <div class="verify-body">

            <p class="verify-message">
                登録していただいたメールアドレスに認証メールを送信しました。<br>
                メール認証を完了してください。
            </p>

            <button class="verify-button">
                認証はこちらから
            </button>

            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="verify-resend">
                    認証メールを再送する
                </button>
            </form>

        </div>

    </div>

</div>

@endsection