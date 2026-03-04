@extends('layouts.user')

@section('content')

<div class="attendance-container">

    <h1 class="page-title">
        <span class="bar">|</span>勤怠一覧</h1>


        <div class="month-nav-wrapper">
                <a href="{{ route('attendance.list', ['month' => $currentMonth->copy()->subMonth()->format('Y-m')]) }}" class="arrow">
                    ←
                </a>

                <div class="month-label">
                    {{ $currentMonth->format('Y年n月') }}
                </div>

                <a href="{{ route('attendance.list', ['month' => $currentMonth->copy()->addMonth()->format('Y-m')]) }}" class="arrow">
                    →
                </a>
        </div>

    <div class="attendance-card">
        <div class="attendance-inner">
            <table class="attendance-table">
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
                        <td>-</td>
                        <td>-</td>
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