@extends('layouts.admin')

@section('title','申請一覧')

@section('css')
<link rel="stylesheet" href="{{ asset('css/stamp_correction_list.css') }}">
@endsection

@section('content')

<div class="list-container">

    <h2 class="list-title">
        <span class="title-bar"></span>
        申請一覧
    </h2>

    <div class="list-card">

        <table class="list-table">

            <thead>
                <tr>
                    <th>日付</th>
                    <th>名前</th>
                    <th>対象</th>
                    <th>申請理由</th>
                    <th>状態</th>
                    <th></th>
                </tr>
            </thead>

            <tbody>

            @foreach($requests as $request)

            <tr>

                <td>
                    {{ \Carbon\Carbon::parse($request->attendance->work_date)->format('m/d') }}
                </td>

                <td>
                    {{ $request->user->name }}
                </td>

                <td>
                    勤怠
                </td>

                <td>
                    {{ $request->reason }}
                </td>

                <td>

                    @if($request->status === 'pending')
                        承認待ち
                    @elseif($request->status === 'approved')
                        承認
                    @else
                        却下
                    @endif

                </td>

                <td>

                    <a href="{{ route('admin.attendance.detail',$request->attendance_id) }}">
                        詳細
                    </a>

                </td>

            </tr>

            @endforeach

            </tbody>

        </table>

    </div>

</div>

@endsection
