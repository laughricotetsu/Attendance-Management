@extends('layouts.admin')

@section('content')
<div class="attendance-list">

    <h2>2023年6月1日の勤怠</h2>

    <table class="attendance-table">
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
        @php
            $attendances = [
                ['id' => 1, 'name' => '山田 太郎', 'start' => '09:00', 'end' => '18:00', 'break' => '1:00', 'total' => '8:00'],
                ['id' => 2, 'name' => '佐藤 花子', 'start' => '09:00', 'end' => '18:00', 'break' => '1:00', 'total' => '8:00'],
                ['id' => 3, 'name' => '鈴木 一郎', 'start' => '09:00', 'end' => '18:00', 'break' => '1:00', 'total' => '8:00'],
            ];
        @endphp

        @foreach ($attendances as $attendance)
            <tr>
                <td>{{ $attendance['name'] }}</td>
                <td>{{ $attendance['start'] }}</td>
                <td>{{ $attendance['end'] }}</td>
                <td>{{ $attendance['break'] }}</td>
                <td>{{ $attendance['total'] }}</td>
                <td>
                    <a href="/admin/attendance/{{ $attendance['id'] }}">詳細</a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

</div>
@endsection
