@extends('layouts.user')

@section('title','勤怠詳細')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance-detail.css') }}">
@endsection

@section('content')

<div class="detail-container">

    <div class="attendance-header">
        <h2 class="detail-title">
            <span class="title-bar"></span>
            <span>勤怠詳細</span>
        </h2>
    </div>

    <div class="detail-card">


        <form method="POST"
            action="{{ route('correction.request.store',$attendance->id) }}">

            @csrf

            <table class="detail-table">

                <tr>
                    <th>名前</th>
                    <td>
                        {{ $attendance->user->name }}
                    </td>
                </tr>

                <tr>
                    <th>日付</th>
                    <td>
                        {{ \Carbon\Carbon::parse($attendance->work_date)->format('Y年m月d日') }}
                    </td>
                </tr>

                {{-- 出勤退勤 --}}
                <tr>
                    <th>出勤・退勤</th>
                    <td>

                        {{-- 出勤 --}}
                        @php
                            $clockIn = optional($attendance->clock_in)->format('H:i');

                            if ($pendingRequest) {
                                foreach ($pendingRequest->details as $detail) {
                                    if ($detail->target_type === 'clock_in') {
                                        $clockIn = $detail->after_value;
                                    }
                                }
                            }
                        @endphp

                        <input type="time" name="clock_in" value="{{ old('clock_in', $clockIn) }}">
                        〜

                        {{-- 退勤 --}}
                        @php
                            $clockOut = optional($attendance->clock_out)->format('H:i');

                            if ($pendingRequest) {
                                foreach ($pendingRequest->details as $detail) {
                                    if ($detail->target_type === 'clock_out') {
                                        $clockOut = $detail->after_value;
                                    }
                                }
                            }
                        @endphp

                        <input type="time" name="clock_out" value="{{ old('clock_out', $clockOut) }}">

                    </td>
                </tr>

                {{-- 休憩 --}}
                @foreach($attendance->breaks as $index => $break)

                    @php
                        // 初期値：元のデータ
                        $breakStart = optional($break->break_start)->format('H:i');
                        $breakEnd   = optional($break->break_end)->format('H:i');

                        // pending の修正申請があれば after_value を適用
                        if ($pendingRequest) {
                            foreach ($pendingRequest->details as $detail) {

                                if ($detail->target_type === 'break_start' && $detail->target_id == $break->id) {
                                    $breakStart = $detail->after_value;
                                }

                                if ($detail->target_type === 'break_end' && $detail->target_id == $break->id) {
                                    $breakEnd = $detail->after_value;
                                }
                            }
                        }
                    @endphp

                    <tr>
                        <th>休憩{{ $index + 1 }}</th>
                        <td>

                            <input type="time"
                                name="breaks[{{ $break->id }}][break_start]"
                                value="{{ old('breaks.'.$break->id.'.break_start', $breakStart) }}"
                                class="@error('breaks.'.$break->id.'break_start') input-error @enderror">

                            〜

                            <input type="time"
                                name="breaks[{{ $break->id }}][break_end]"
                                value="{{ old('breaks.'.$break->id.'.break_end', $breakEnd) }}"
                                class="@error('breaks.'.$break->id.'.break_end') input-error @enderror">

                            @error('breaks.' . $break->id . '.break_start')
                            <p class="error-message">{{ $message }}</p>
                            @enderror

                            @error('breaks.' . $break->id . '.break_end')
                            <p class="error-message">{{ $message }}</p>
                            @enderror

                        </td>
                    </tr>

                @endforeach

                {{-- 休憩追加 --}}
                @if(!$isAdmin)

                <tr>

                    <th>
                        休憩{{ $attendance->breaks->count() + 1 }}
                    </th>

                    <td>

                        <input type="time"
                        name="breaks[new][break_start]">

                        〜

                        <input type="time"
                        name="breaks[new][break_end]">

                            @error('breaks.new.break_start')
                            <p class="error-message">{{ $message }}</p>
                            @enderror

                            @error('breaks.new.break_end')
                            <p class="error-message">{{ $message }}</p>
                            @enderror
                    </td>

                </tr>

                @endif


                {{-- 備考 --}}
                <tr>

                    <th>
                        備考
                    </th>

                    <td>

                @php
                $remarks = $attendance->remarks;

                if ($pendingRequest) {
                    foreach ($pendingRequest->details as $detail) {
                        if ($detail->target_type === 'remarks') {
                            $remarks = $detail->after_value;
                        }
                    }
                }
                @endphp

                <textarea name="remarks">{{ old('remarks', $remarks) }}</textarea>


                        @error('remarks')
                        <p class="error-message">{{ $message }}</p>
                        @enderror

                    </td>

                </tr>

            </table>


            {{-- ボタンエリア --}}
            <div class="attendance-button-area">

            {{-- 管理者 --}}
            @if($isAdmin && $correctionRequest)

                <form method="POST"
                    action="{{ route('admin.correction.request.approve',$correctionRequest->id) }}">

                    @csrf

                    <button class="approve-button">
                        承認
                    </button>

                </form>

            {{-- 一般ユーザー --}}
            @else

                @if(!$pendingRequest)

                <button type="submit" class="edit-button">
                    修正
                </button>

                @else

            @if($pendingRequest)

                <div class="diff-container">
                    <h3>修正申請内容（承認待ち）</h3>

                    <table class="diff-table">

                        @foreach($pendingRequest->details as $detail)

                            @php
                                $before = $detail->before_value;
                                $after  = $detail->after_value;

                                $isChanged = $before !== $after;

                                // ラベル
                                switch ($detail->target_type) {
                                    case 'clock_in':
                                        $label = '出勤';
                                        break;

                                    case 'clock_out':
                                        $label = '退勤';
                                        break;

                                    case 'break_start':
                                        $label = '休憩開始';
                                        break;

                                    case 'break_end':
                                        $label = '休憩終了';
                                        break;

                                    case 'remarks':
                                        $label = '備考';
                                        break;
                                }

                                // 表示整形
                                if ($detail->target_type === 'remarks') {
                                    $beforeDisp = $before ?: '（空）';
                                    $afterDisp  = $after ?: '（空）';
                                } else {
                                    $beforeDisp = $before ? \Carbon\Carbon::parse($before)->format('H:i') : '（空）';
                                    $afterDisp  = $after  ? \Carbon\Carbon::parse($after)->format('H:i')  : '（空）';
                                }
                            @endphp

                            <tr class="{{ $isChanged ? 'diff-row-changed' : '' }}">
                                <th>{{ $label }}</th>
                                <td>
                                    <span class="diff-old">{{ $beforeDisp }}</span>
                                    →
                                    <span class="diff-new">{{ $afterDisp }}</span>

                                    @if($isChanged)
                                        <span class="diff-label">変更</span>
                                    @endif
                                </td>
                            </tr>

                        @endforeach

                    </table>
                </div>

            @endif

                <button class="pending-button" disabled>
                    ＊承認待ちのため修正はできません。

                @endif

            @endif

            </div>

        </form>

    </div>

</div>

@endsection