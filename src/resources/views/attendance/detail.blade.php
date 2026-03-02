@extends('layouts.user')

@section('content')
<div class="attendance-wrapper">

    <h2 class="attendance-title">
        <span class="title-bar"></span>
        勤怠詳細
    </h2>

    <div class="attendance-card">

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
                    {{ $attendance->clock_in ?? '--:--' }}
                    〜
                    {{ $attendance->clock_out ?? '--:--' }}
                </td>
            </tr>

            {{-- 休憩一覧 --}}
            @foreach($attendance->breaks as $index => $break)
            <tr>
                <th>休憩{{ $index + 1 }}</th>
                <td>
                    {{ $break->break_start }}
                    〜
                    {{ $break->break_end ?? '--:--' }}
                </td>
            </tr>
            @endforeach

            <tr>
                <th>備考</th>
                <td>{{ $attendance->remarks ?? '未入力' }}</td>
            </tr>

        </table>

    </div>

    <div class="attendance-button-area">
        <button class="edit-button">修正</button>
    </div>

</div>
@endsection