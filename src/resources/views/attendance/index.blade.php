@extends('layouts.user')

@section('content')

<div class="attendance-wrapper">
    <div class="attendance-box">

        {{-- 出勤前 --}}
        @if(!$attendance || !$attendance->clock_in)

            <div class="status-badge">勤務外</div>

            <div class="attendance-date">
                {{ \Carbon\Carbon::now()->isoFormat('Y年M月D日(ddd)') }}
            </div>

            <div class="attendance-time" id="current-time">
                {{ \Carbon\Carbon::now()->format('H:i') }}
            </div>

            <form method="POST" action="{{ route('attendance.start') }}">
                @csrf
                <button class="attendance-button">
                    出勤
                </button>
            </form>

        {{-- 退勤済み --}}
        @elseif($attendance && $attendance->clock_out)

            <div class="status-badge">退勤済</div>

            <div class="attendance-date">
                {{ \Carbon\Carbon::now()->isoFormat('Y年M月D日(ddd)') }}
            </div>

            <div class="attendance-time" id="current-time">
                {{ \Carbon\Carbon::now()->format('H:i') }}
            </div>

            <div class="message">
                お疲れ様でした。
            </div>

        {{-- 出勤中・休憩中 --}}
        @else

            <div class="status-badge">
                @if ($status === 'working')
                    出勤中
                @elseif ($status === 'break')
                    休憩中
                @endif
            </div>

            <div class="attendance-date">
                {{ \Carbon\Carbon::now()->isoFormat('Y年M月D日(ddd)') }}
            </div>

            <div class="attendance-time" id="current-time">
                {{ \Carbon\Carbon::now()->format('H:i') }}
            </div>

            @if ($status === 'working')
                <div class="button-group">
                    <form method="POST" action="{{ route('attendance.finish') }}">
                        @csrf
                        <button class="attendance-button btn-black">
                            退勤
                        </button>
                    </form>

                    <form method="POST" action="{{ route('attendance.break.start') }}">
                        @csrf
                        <button class="attendance-button white-button">
                            休憩入
                        </button>
                    </form>
                </div>
            @endif

            @if ($status === 'break')
                <div class="button-group">
                    <form method="POST" action="{{ route('attendance.break.end') }}">
                        @csrf
                        <button class="attendance-button white-button">
                            休憩戻
                        </button>
                    </form>
                </div>
            @endif

        @endif

    </div>
</div>

<script>
function updateTime() {
    const now = new Date();
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    document.getElementById('current-time').textContent =
        hours + ':' + minutes;
}
setInterval(updateTime, 1000);
</script>

@endsection
