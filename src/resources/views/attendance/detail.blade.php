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

                        @if ($errors->any())
                    <div class="error-area">

                        @foreach ($errors->all() as $error)
                            <p class="error-message">{{ $error }}</p>
                        @endforeach

                    </div>
                    @endif

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

                        <input type="time" name="clock_in"
                        value="{{ old('clock_in', optional($attendance->clock_in)->format('H:i')) }}"
                        class="@error('clock_in') input-error @enderror">


                        〜

                        <input type="time" name="clock_out"
                        value="{{ old('clock_out', optional($attendance->clock_out)->format('H:i')) }}"
                        class="@error('clock_in') input-error @enderror">

                        @error('clock_in')
                        <p class="error-message">{{ $message }}</p>
                        @enderror

                    </td>
                </tr>

                {{-- 休憩 --}}
                @foreach($attendance->breaks as $index => $break)

                <tr>

                    <th>
                        休憩{{ $index + 1 }}
                    </th>

                    <td>

                        <input type="time"
                        name="breaks[{{ $break->id }}][break_start]"
                        value="{{ old('breaks.'.$break->id.'.break_start', optional($break->break_start)->format('H:i')) }}"
                        class="@error('break_start') input-error @enderror">


                        〜

                        <input type="time"
                        name="breaks[{{ $break->id }}][break_end]"
                        value="{{ old('breaks.'.$break->id.'.break_end', optional($break->break_end)->format('H:i')) }}"
                        class="@error('break_end') input-error @enderror">

                        @error('break_start')
                        <p class="error-message">{{ $message }}</p>
                        @enderror

                        @error('break_end')
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

                    </td>

                </tr>

                @endif


                {{-- 備考 --}}
                <tr>

                    <th>
                        備考
                    </th>

                    <td>

                        <textarea name="remarks"
                        class="@error('remarks') input-error @enderror">{{ old('remarks', $attendance->remarks) }}</textarea>

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

                <button class="pending-button" disabled>
                    ＊承認待ちのため修正はできません。

                @endif

            @endif

            </div>

        </form>

    </div>

</div>

@endsection