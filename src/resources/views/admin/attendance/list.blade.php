@extends('layouts.admin')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin_list.css') }}">
@endsection

@section('content')
<div class="attendance-container">

    <h1 class="page-title">
        <span class="bar">|</span>
        {{ \Carbon\Carbon::parse($date)->format('Y年n月j日') }}の勤怠
    </h1>

    <div class="date-nav-wrapper">

        <a href="{{ route('admin.attendance.list', ['date' => $prevDate]) }}" class="nav-link">
            ← 前日
        </a>

        <div class="date-center">
                            <svg class="calendar-icon" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M6.75 3v2.25M17.25 3v2.25M3.75 8.25h16.5M4.5 5.25h15A2.25 2.25 0 0121.75 7.5v11.25A2.25 2.25 0 0119.5 21h-15A2.25 2.25 0 012.25 18.75V7.5A2.25 2.25 0 014.5 5.25z" />
                </svg>

            {{ \Carbon\Carbon::parse($date)->format('Y/m/j') }}
        </div>

        <a href="{{ route('admin.attendance.list', ['date' => $nextDate]) }}" class="nav-link">
            翌日 →
        </a>

    </div>

    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th>名前</th>
                    <th>出勤</th>
                    <th>退勤</th>
                    <th>休憩</th>
                    <th>合計</th>
                    <th>詳細</th>
                </tr>
            </thead>
            <tbody>
        @foreach ($users as $user)

            @php
                $attendance = $user->attendances->first();
            @endphp

            <tr>
                <td>{{ $user->name }}</td>

                <td>
                @if($attendance && $attendance->clock_in)
                    {{ \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') }}
                @endif
                </td>

                <td>
                @if($attendance && $attendance->clock_out)
                    {{ \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') }}
                @endif
                </td>

                <td>{{ $attendance?->break_duration ?? '' }}</td>

                <td>{{ $attendance?->work_duration ?? '' }}</td>

                <td>
                @if($attendance)
                    <a href="{{ route('admin.attendance.detail', $attendance->id) }}">
                        詳細
                    </a>
                @endif
                </td>
            </tr>

        @endforeach

        </tbody>
        </table>
    </div>

</div>

@endsection
