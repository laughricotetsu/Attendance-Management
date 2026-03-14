@extends('layouts.user')

@section('title', '勤怠一覧')

@section('css')
<link rel="stylesheet" href="{{ asset('css/user_list.css') }}">
@endsection

@section('content')

<div class="list-container">

    <h1 class="list-title">
        <span class="bar">|</span>勤怠一覧</h1>


        <div class="list-month-nav">
                <a href="{{ route('attendance.list', ['month' => $currentMonth->copy()->subMonth()->format('Y-m')]) }}" class="arrow">
                    ←
                </a>

            <div class="list-month-label">
                <svg class="calendar-icon" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M6.75 3v2.25M17.25 3v2.25M3.75 8.25h16.5M4.5 5.25h15A2.25 2.25 0 0121.75 7.5v11.25A2.25 2.25 0 0119.5 21h-15A2.25 2.25 0 012.25 18.75V7.5A2.25 2.25 0 014.5 5.25z" />
                </svg>

                {{ $currentMonth->format('Y/m') }}
            </div>

                <a href="{{ route('attendance.list', ['month' => $currentMonth->copy()->addMonth()->format('Y-m')]) }}" class="arrow">
                    →
                </a>
        </div>

    <div class="list-card">
        <div class="attendance-inner">
            <table class="list-table">
                <thead>
                    <tr>
                        <th>日付</th>
                        <th>出勤</th>
                        <th>退勤</th>
                        <th>休憩</th>
                        <th>合計</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($attendances as $attendance)
                    <tr>
                        <td>@php
                                $date = \Carbon\Carbon::parse($attendance->work_date);
                            @endphp

                            {{ $date->format('m/d') }} ({{ $date->isoFormat('ddd') }})</td>
                        <td>{{ $attendance->clock_in ? \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') : '-' }}</td>
                        <td>{{ $attendance->clock_out ? \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') : '-' }}</td>
                        <td>{{ $attendance->break_duration }}</td>
                        <td>{{ $attendance->work_duration }}</td>
                        <td><a href="{{ route('attendance.detail', $attendance->id) }}"
                        class="detail-button">
                            詳細
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection