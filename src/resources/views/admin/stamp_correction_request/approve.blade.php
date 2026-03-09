@extends('layouts.admin')

@section('content')

<h2>修正申請承認</h2>

<p>申請ID: {{ $request->id }}</p>

<p>申請者: {{ $request->user->name }}</p>

<p>理由: {{ $request->reason }}</p>

@if($request->status === 'pending')

<form method="POST" action="{{ route('correction.request.approve',$request->id) }}">
@csrf
<button type="submit">承認</button>
</form>

@else

<p>この申請は承認済みです</p>

@endif
@endsection