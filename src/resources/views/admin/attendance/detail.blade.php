@extends('layouts.admin')

@section('title', '勤怠詳細')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin_detail.css') }}">
@endsection

@section('content')
<div class="admin-container">
    <h1 class="page-title">勤怠詳細</h1>

    <div class="attendance-card">
        {{-- 名前 --}}
        <div class="row">
            <div class="label">名前</div>
            <div class="value">{{ $attendance->user->name }}</div>
        </div>

        {{-- 日付 --}}
        <div class="row">
            <div class="label">日付</div>
            <div class="value">
                {{ \Carbon\Carbon::parse($attendance->work_date)->format('Y年n月j日') }}
            </div>
        </div>
    <form method="POST" action="{{ route('admin.attendance.update', $attendance->id) }}">
        @csrf
        @method('PUT')
        {{-- 出勤・退勤 --}}
        <div class="row">
            <div class="label">出勤・退勤</div>
            <div class="value time-range">
                <input type="time" name="clock_in" value="{{ optional($attendance->clock_in)->format('H:i') }}">
                <span>〜</span>
                <input type="time" name="clock_out" value="{{ optional($attendance->clock_out)->format('H:i') }}">
            </div>
        </div>

        {{-- 休憩 --}}
        @foreach ($attendance->breaks as $index => $break)
            <div class="row">
                <div class="label">休憩{{ $index + 1 }}</div>
                <div class="value time-range">
                    <input type="time" value="{{ optional($break->break_start)->format('H:i') }}">
                    <span>〜</span>
                    <input type="time" value="{{ optional($break->break_end)->format('H:i') }}">
                </div>
            </div>
        @endforeach

        {{-- 追加用の空休憩 --}}
        <div class="row">
            <div class="label">休憩{{ $attendance->breaks->count() + 1 }}</div>
            <div class="value time-range">
                <input type="time">
                <span>〜</span>
                <input type="time">
            </div>
        </div>

        {{-- 備考 --}}
        <div class="row">
            <div class="label">備考</div>
            <div class="value">
                <input type="text" name="note" value="{{ $attendance->note ?? '' }}">
            </div>
        </div>
    </div>
        <div class="button-area">
            @if (auth()->user()->role === 'admin')
                <button type="submit" class="btn-black">
                    修正
                </button>
            @else
                <button
                    type="submit"
                    formaction="{{ route('admin.attendance.request', $attendance->id) }}"
                    formmethod="POST"
                    class="btn-black"
                >
                    修正申請
                </button>
            @endif
        </div>

    </form>
</div>
@endsection
