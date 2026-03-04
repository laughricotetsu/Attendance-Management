@extends('layouts.user')

@section('content')
<div class="attendance-wrapper">

    <h2 class="attendance-title">
        <span class="title-bar"></span>
        勤怠詳細
    </h2>

    <div class="attendance-card">

        <form method="POST" action="{{ route('attendance.update', $attendance->id) }}">
        @csrf
        @method('PATCH')

        <table class="attendance-table">

            <tr>
                <th>名前</th>
                <td>{{ $attendance->user->name }}</td>
            </tr>

            <tr>
                <th>日付</th>
                <td>
                    {{ \Carbon\Carbon::parse($attendance->work_date)->format('Y年m月d日') }}
                </td>
            </tr>

            <tr>
                <th>出勤・退勤</th>
                <td>
                <input type="time" name="clock_in"
                        value="{{ old('clock_in', optional($attendance->clock_in)->format('H:i')) }}">

                        〜

                <input type="time" name="clock_out"
                        value="{{ old('clock_out', optional($attendance->clock_out)->format('H:i')) }}">
                </td>
            </td>
            </tr>
        {{-- 既存休憩 --}}
        @foreach($attendance->breaks as $index => $break)
        <tr>
            <th>休憩{{ $index + 1 }}</th>
        <td>
            <input type="time"
                name="breaks[{{ $break->id }}][break_start]"
                value="{{ $break->break_start ? \Carbon\Carbon::parse($break->break_start)->format('H:i') : '' }}">
        〜
            <input type="time"
                name="breaks[{{ $break->id }}][break_end]"
                value="{{ $break->break_end ? \Carbon\Carbon::parse($break->break_end)->format('H:i') : '' }}">
        </td>
        </tr>
        @endforeach

        {{-- 新規追加用 --}}
        <tr>
            <th>休憩{{ $attendance->breaks->count() + 1 }}</th>
            <td>
                <input type="time" name="breaks[new][break_start]">
                〜
                <input type="time" name="breaks[new][break_end]">
            </td>
        </tr>

        <tr>
            <th>備考</th>
            <td>
                <textarea name="remarks">{{ old('remarks', $attendance->remarks) }}</textarea>
            </td>
        </tr>

        </table>

        <div class="attendance-button-area">
            <button type="submit" class="edit-button">修正</button>
        </div>

    </form>

@endsection