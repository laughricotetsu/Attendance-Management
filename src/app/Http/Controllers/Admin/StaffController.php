<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StaffController extends Controller
{

    // スタッフ一覧
    public function index()
    {
        $users = User::where('role','user')
            ->paginate(10);

        return view('admin.staff.list',compact('users'));
    }


    // スタッフ勤怠一覧
    public function attendance(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // 月取得
        $month = $request->month
            ? Carbon::parse($request->month)
            : Carbon::now();

        $start = $month->copy()->startOfMonth();
        $end = $month->copy()->endOfMonth();

        // 前月・次月
        $prevMonth = $month->copy()->subMonth()->format('Y-m');
        $nextMonth = $month->copy()->addMonth()->format('Y-m');

        // 勤怠取得
        $attendances = Attendance::where('user_id',$id)
            ->whereBetween('work_date',[$start,$end])
            ->orderBy('work_date')
            ->get();

        return view(
            'admin.staff.attendance',
            compact(
                'user',
                'attendances',
                'month',
                'prevMonth',
                'nextMonth'
            )
        );
    }
}