@extends('layouts.admin')

@section('css')
<link rel="stylesheet" href="{{ asset('css/staff_list.css') }}">
@endsection


@section('content')


<div class="staff-container">

    <h2 class="page-title">スタッフ一覧</h2>

        <div class="staff-card">

        <table class="staff-table">

            <thead>

                <tr>
                <th>名前</th>
                <th>メールアドレス</th>
                <th>勤怠</th>
                </tr>

            </thead>

            <tbody>

                @foreach($staffs as $staff)

                <tr>

                    <td>
                        {{ $staff->name }}
                    </td>

                    <td>
                        {{ $staff->email }}
                    </td>

                    <td>

                        <a href="{{ route('admin.attendance.list',['user_id'=>$staff->id]) }}"
                        class="detail-link">

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