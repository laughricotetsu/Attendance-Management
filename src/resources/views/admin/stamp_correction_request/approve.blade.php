@extends('layouts.admin')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance_detail.css') }}">
@endsection

@section('content')

<div class="attendance-detail-container">

<h1 class="page-title">
<span class="bar">|</span> 勤怠詳細
</h1>

<div class="detail-card">

<table class="detail-table">

<tr>
<th>名前</th>
<td>{{ $request->user->name }}</td>
</tr>

<tr>
<th>日付</th>
<td>
{{ \Carbon\Carbon::parse($attendance->work_date)->format('Y年') }}
&nbsp;
{{ \Carbon\Carbon::parse($attendance->work_date)->format('n月j日') }}
</td>
</tr>

<tr>
<th>出勤・退勤</th>
<td>
{{ optional($attendance->clock_in)->format('H:i') }}
〜
{{ optional($attendance->clock_out)->format('H:i') }}
</td>
</tr>

@foreach($attendance->breaks as $index => $break)

<tr>
<th>休憩{{ $index+1 }}</th>
<td>
{{ optional($break->break_start)->format('H:i') }}
〜
{{ optional($break->break_end)->format('H:i') }}
</td>
</tr>

@endforeach

<tr>
<th>備考</th>
<td>
{{ $request->reason }}
</td>
</tr>

</table>

</div>

<div class="approve-area">

<form method="POST"
action="{{ route('correction.request.approve',$request->id) }}">

@csrf

<button class="approve-btn">
承認
</button>

</form>

</div>

</div>

@endsection