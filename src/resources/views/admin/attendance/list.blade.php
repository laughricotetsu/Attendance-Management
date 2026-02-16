@extends('layouts.admin')

@section('content')
<div class="attendance-list">

    <h2>勤怠一覧</h2>

    <table>
        <thead>
            <tr>
                <th>名前</th>
                <th>日付</th>
                <th>出勤</th>
                <th>退勤</th>
                <th>休憩</th>
                <th>合計</th>
            </tr>
        </thead>

        <tbody>
        @foreach ($attendances as $attendance)
        <tr>
            <td>{{ $attendance->user->name }}</td>
            <td>{{ $attendance->work_date }}</td>
            <td>{{ $attendance->clock_in }}</td>
            <td>{{ $attendance->clock_out }}</td>
            <td>{{ $attendance->break_duration }}</td>
            <td>{{ $attendance->work_duration }}</td>
        <td>
                <a href="{{ route('admin.attendance.detail', $attendance->id) }}">
                    詳細
                </a>
            </td>
        </tr>
        @endforeach
    </tbody>
    </table>

</div>
@endsection
