@extends('layouts.admin')

@section('title','申請一覧')

@section('content')

<div class="admin-container">

    <h2 class="page-title">
        <span class="title-bar"></span>
        申請一覧
    </h2>

    <div class="admin-card">

        <table class="admin-table">

            <thead>
                <tr>
                    <th>名前</th>
                    <th>対象日</th>
                    <th>申請理由</th>
                    <th>申請日時</th>
                    <th>詳細</th>
                </tr>
            </thead>

            <tbody>

            @foreach($requests as $request)

            <tr>

                <td>{{ $request->user->name }}</td>

                <td>{{ $request->attendance->work_date }}</td>

                <td>{{ $request->reason }}</td>

                <td>{{ $request->created_at }}</td>

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
                    <a href="{{ route('admin.correction.request.show', $request->id) }}">
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