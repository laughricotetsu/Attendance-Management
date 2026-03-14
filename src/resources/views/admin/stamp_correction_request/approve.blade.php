@extends('layouts.admin')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance-detail.css') }}">
@endsection

@section('content')

<div class="attendance-container">

        <h1 class="page-title">
            <span class="bar">|</span>
            勤怠詳細
        </h1>

            <div class="detail-card">

                <table class="detail-table">

                    <tr>
                        <th>名前</th>
                        <td>{{ $request->attendance->user->name }}</td>
                    </tr>

                    <tr>
                        <th>日付</th>
                        <td>
                            {{ \Carbon\Carbon::parse($request->attendance->work_date)->format('Y年n月j日') }}
                        </td>
                    </tr>

                    <tr>
                        <th>出勤・退勤</th>

                            <td>

                                @php
                                $clockIn = $request->attendance->clock_in;
                                $clockOut = $request->attendance->clock_out;
                                @endphp

                                @foreach($request->details as $detail)

                                @if($detail->target_type === 'clock_in')
                                @php $clockIn = $detail->after_value; @endphp
                                @endif

                                @if($detail->target_type === 'clock_out')
                                @php $clockOut = $detail->after_value; @endphp
                                @endif

                                @endforeach

                                {{ \Carbon\Carbon::parse($clockIn)->format('H:i') }}
                                〜
                                {{ \Carbon\Carbon::parse($clockOut)->format('H:i') }}

                            </td>
                    </tr>

                    @foreach($request->attendance->breaks as $index => $break)

                    <tr>

                        <th>休憩{{ $index + 1 }}</th>

                            <td>

                                @php
                                $start = $break->break_start;
                                $end = $break->break_end;
                                @endphp

                                @foreach($request->details as $detail)

                                @if($detail->target_type === 'break_start' && $detail->target_id == $break->id)
                                @php $start = $detail->after_value; @endphp
                                @endif

                                @if($detail->target_type === 'break_end' && $detail->target_id == $break->id)
                                @php $end = $detail->after_value; @endphp
                                @endif

                                @endforeach

                                {{ $start ? \Carbon\Carbon::parse($start)->format('H:i') : '' }}
                                〜
                                {{ $end ? \Carbon\Carbon::parse($end)->format('H:i') : '' }}

                            </td>

                        </tr>

                    @endforeach

                    <tr>

                        <th>備考</th>

                        <td>{{ $request->reason }}</td>

                    </tr>

                </table>

            </div>


            <div class="approve-area">

                @if($request->status === 'pending')

                <form method="POST" action="{{ route('admin.correction.request.approve.update',$request->id) }}">
                @csrf

                    <button class="approve-btn">
                    承認
                    </button>

                </form>

                @else

                    <span class="approved">
                    承認済み
                    </span>

                @endif

            </div>

</div>

@endsection