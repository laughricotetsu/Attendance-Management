@extends('layouts.admin')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin_list.css') }}">
@endsection

@section('content')

    <div class="attendance-container">

        <h1 class="page-title">
        <span class="bar">|</span>
        {{ $user->name }}さんの勤怠
        </h1>


        <div class="date-nav-wrapper">

            <a href="{{ route('admin.staff.attendance',[
                'id'=>$user->id,
                'month'=>$prevMonth
            ]) }}" class="nav-link">
            ← 前月
            </a>


                <div class="date-center">
                {{ $month->format('Y年n月') }}
                </div>


            <a href="{{ route('admin.staff.attendance',[
                'id'=>$user->id,
                'month'=>$nextMonth
            ]) }}" class="nav-link">
            翌月 →
            </a>

        </div>


        <div class="table-card">

            <table>

                    <thead>
                        <tr>
                            <th>日付</th>
                            <th>出勤</th>
                            <th>退勤</th>
                            <th>休憩</th>
                            <th>合計</th>
                            <th>詳細</th>
                        </tr>
                    </thead>

                <tbody>

                    @foreach ($attendances as $attendance)

                        <tr>

                            <td>
                                {{ \Carbon\Carbon::parse($attendance->work_date)->format('m/d') }}
                            </td>

                            <td>
                                {{ optional($attendance->clock_in)->format('H:i') }}
                            </td>

                            <td>
                                {{ optional($attendance->clock_out)->format('H:i') }}
                            </td>

                            <td>
                                {{ $attendance->break_duration ?? '-' }}
                            </td>

                            <td>
                                {{ $attendance->work_duration ?? '-' }}
                            </td>

                            <td>
                                <a href="{{ route('admin.attendance.detail',$attendance->id) }}">
                                詳細
                                </a>
                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

            <div class="csv-button-area">

                <a href="{{ route('admin.staff.attendance.csv',[
                    'id'=>$user->id,
                    'month'=>$month->format('Y-m')
                ]) }}" class="csv-button">

                CSV出力

                </a>

            </div>

        </div>

    </div>

@endsection