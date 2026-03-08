@extends('layouts.user')

@section('title','申請一覧')

@section('css')
<link rel="stylesheet" href="{{ asset('css/correction_request.css') }}">
@endsection


@section('content')

<div class="container">

    <h2 class="page-title">
        <span class="title-bar"></span>
        申請一覧
    </h2>

    <div class="tab-menu">
        <a href="?status=pending" class="{{ request('status')=='pending' ? 'active' : '' }}">
        承認待ち
        </a>

        <a href="?status=approved" class="{{ request('status')=='approved' ? 'active' : '' }}">
            承認済み
        </a>
    </div>

        <table class="request-table">

            <thead>
                <tr>
                    <th>状態</th>
                    <th>名前</th>
                    <th>対象日時</th>
                    <th>申請理由</th>
                    <th>申請日時</th>
                    <th>詳細</th>
                </tr>
            </thead>

        <tbody>

        @foreach($requests as $request)

    <tr>

        <td>
            @if($request->status == 'pending')
            承認待ち
            @else
            承認済
            @endif
        </td>

        <td>
            {{ $request->user->name }}
        </td>

        <td>
            {{ \Carbon\Carbon::parse($request->attendance->work_date)->format('Y/m/d') }}
        </td>

        <td>
            {{ $request->reason }}
        </td>

        <td>
            {{ $request->created_at->format('Y/m/d') }}
        </td>

        <td>
            <a href="{{ route('attendance.detail',$request->attendance_id) }}">
            詳細
            </a>
        </td>

    </tr>

        @endforeach

        </tbody>

        </table>

</div>

@endsection