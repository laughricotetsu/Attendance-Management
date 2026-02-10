@extends('layouts.guest')

@section('title', 'ログイン')

@section('content')
    <h1>ログイン</h1>
@endsection

<form method="POST" action="/login">
    @csrf
    <button type="submit">ログイン</button>
</form>
