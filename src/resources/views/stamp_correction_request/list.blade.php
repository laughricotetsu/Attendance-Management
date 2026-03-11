@php
$layout = Auth::user()->role === 'admin'
    ? 'layouts.admin'
    : 'layouts.user';
@endphp

@extends($layout)

@section('css')
<link rel="stylesheet" href="{{ asset('css/stamp_correction_list.css') }}">
@endsection


@section('content')

    <div class="request-container">

        <h2 class="page-title">申請一覧</h2>


        <div class="tab-menu">

            <a href="{{ route('correction.request.list',['status'=>'pending']) }}"
            class="{{ $status === 'pending' ? 'active' : '' }}">
            承認待ち
            </a>

            <a href="{{ route('correction.request.list',['status'=>'approved']) }}"
            class="{{ $status === 'approved' ? 'active' : '' }}">
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

    @foreach ($requests as $request)

        <tr>

            <td>
                @if($request->status === 'pending')
                <span class="status pending">承認待ち</span>
                @else
                <span class="status approved">承認済</span>
                @endif
            </td>

            <td>
                {{ $request->user->name ?? Auth::user()->name }}
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

                @if(Auth::user()->role === 'admin')

                <a class="detail-btn"
                href="{{ route('admin.correction.request.approve',$request->id) }}">
                詳細
                </a>

                @else

                <a class="detail-btn"
                href="{{ route('attendance.detail',$request->attendance->id) }}">
                詳細
                </a>

                @endif

            </td>

        </tr>

    @endforeach

    </tbody>

    </table>

</div>

@endsection