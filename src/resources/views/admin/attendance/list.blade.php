@extends('layouts.admin')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin_list.css') }}">
@endsection

@section('content')
<div class="attendance-wrapper">

    <h1 class="page-title">
        <span class="bar">|</span>
        {{ \Carbon\Carbon::parse($date)->format('Y年n月j日') }}の勤怠
    </h1>

    <div class="date-nav-wrapper">
        <a href="#" class="nav-link">← 前日</a>

        <div class="date-center">
            <span class="calendar-icon">📅</span>
            {{ \Carbon\Carbon::parse($date)->format('Y/m/d') }}
        </div>

        <a href="#" class="nav-link">翌日 →</a>
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

                <td>{{ optional($attendance?->clock_in)->format('H:i') }}</td>

                <td>{{ optional($attendance?->clock_out)->format('H:i') }}</td>

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
