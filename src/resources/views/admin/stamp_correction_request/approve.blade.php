@extends('layouts.admin')

@section('title','申請承認')

@section('content')

    <div class="admin-container">

        <h2 class="page-title">
            <span class="title-bar"></span>
            申請詳細
        </h2>

    <div class="admin-card">

    <table class="admin-table">

        <tr>
        <th>名前</th>
        <td>{{ $request->user->name }}</td>
        </tr>

        <tr>
        <th>申請理由</th>
        <td>{{ $request->reason }}</td>
        </tr>

    </table>

    <h3>変更内容</h3>

    <table class="admin-table">

        <thead>
        <tr>
        <th>対象</th>
        <th>変更前</th>
        <th>変更後</th>
        </tr>
        </thead>

    <tbody>

    @foreach($request->details as $detail)

    <tr>

        <td>{{ $detail->target_type }}</td>

        <td>{{ $detail->before_value }}</td>

        <td>{{ $detail->after_value }}</td>

    </tr>

    @endforeach

    </tbody>

    </table>

        <form method="POST"
        action="{{ route('admin.correction.request.approve',$request->id) }}">

        @csrf

            <button class="approve-button">
            承認
            </button>

        </form>

    </div>

</div>

@endsection