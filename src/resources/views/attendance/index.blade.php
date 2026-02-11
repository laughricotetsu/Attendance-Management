@extends('layouts.user')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
@endsection

@section('content')
<div class="attendance-container">

    <div class="status-badge">
        勤務外
    </div>

    <div class="date">
        {{ now()->format('Y年n月j日') }}（{{ ['日','月','火','水','木','金','土'][now()->dayOfWeek] }}）
    </div>

    <div class="time">
        {{ now()->format('H:i') }}
    </div>

    <form method="POST" action="#">
        @csrf
        <button class="primary-button">
            出勤
        </button>
    </form>

</div>
@endsection
