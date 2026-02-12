@extends('layouts.user')

@section('content')

<div class="attendance-wrapper">

    <div class="attendance-box">

        {{-- ステータス --}}
        <div class="status-badge">
            {{ !$attendance || !$attendance->clock_in ? '勤務外' : '出勤中' }}
        </div>

        {{-- 日付 --}}
        <div class="date">
            {{ \Carbon\Carbon::now()->format('Y年n月j日(D)') }}
        </div>

        {{-- 時刻 --}}
        <div class="time" id="current-time">
            {{ \Carbon\Carbon::now()->format('H:i') }}
        </div>

        {{-- 出勤ボタン --}}
        @if(!$attendance || !$attendance->clock_in)
            <form method="POST" action="{{ route('attendance.start') }}">
                @csrf
                <button class="attendance-button">
                    出勤
                </button>
            </form>
        @endif

    </div>

</div>

<script>
    // リアルタイム時計
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
