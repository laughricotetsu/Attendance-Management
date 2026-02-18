<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;


class AttendanceController extends Controller
    {

    public function index(Request $request)
    {
        $date = $request->date
            ? Carbon::parse($request->date)
            : Carbon::today();

        $prevDate = $date->copy()->subDay()->format('Y-m-d');
        $nextDate = $date->copy()->addDay()->format('Y-m-d');

        // 全ユーザー取得 + その日の勤怠だけ取得
        $users = User::with(['attendances' => function ($query) use ($date) {
            $query->whereDate('work_date', $date)
                ->with('breaks');
        }])->get();

        return view('admin.attendance.list', compact(
            'users',
            'date',
            'prevDate',
            'nextDate'
        ));
    }

    public function show(Attendance $attendance)
    {
        $attendance->load(['user', 'breaks']);

        return view('admin.attendance.detail', compact('attendance'));
    }
    public function update(Request $request, Attendance $attendance)
    {

        $user = auth()->user();

        if ($user->role === 'admin') {

            $attendance->update([
                'clock_in'  => $request->clock_in,
                'clock_out' => $request->clock_out,
                'note'      => $request->note,
            ]);

            // 既存break更新
            if ($request->breaks) {
                foreach ($request->breaks as $breakId => $breakData) {

                    $break = \App\Models\BreakTime::find($breakId);

                    if ($break) {
                        $break->update([
                            'break_start' => $breakData['break_start'],
                            'break_end'   => $breakData['break_end'],
                        ]);
                    }
                }
            }

            // 🔥 新規break追加
            if (
                isset($request->new_break['break_start']) &&
                isset($request->new_break['break_end']) &&
                $request->new_break['break_start'] &&
                $request->new_break['break_end']
            ) {
                $attendance->breaks()->create([
                    'break_start' => $request->new_break['break_start'],
                    'break_end'   => $request->new_break['break_end'],
                ]);
            }

            return redirect()
                ->route('admin.attendance.detail', $attendance->id)
                ->with('success', '勤怠を修正しました');
        }

        // 一般ユーザー：修正申請
        $requestHeader = AttendanceCorrectionRequest::create([
            'attendance_id' => $attendance->id,
            'user_id'       => $user->id,
            'status'        => 'pending',
        ]);

        // 詳細（出勤・退勤）
        AttendanceCorrectionRequestDetail::create([
            'request_id'   => $requestHeader->id,
            'target_type'  => 'attendance',
            'target_id'    => $attendance->id,
            'before_value' => json_encode([
                'clock_in'  => $attendance->clock_in,
                'clock_out' => $attendance->clock_out,
            ]),
            'after_value'  => json_encode([
                'clock_in'  => $request->clock_in,
                'clock_out' => $request->clock_out,
            ]),
        ]);

        return redirect()
            ->route('attendance.detail', $attendance->id)
            ->with('success', '修正申請を送信しました');
    }

    public function requestCorrection(Request $request, Attendance $attendance)
    {
        // ① 修正申請ヘッダ作成
        $correctionRequest = AttendanceCorrectionRequest::create([
            'attendance_id' => $attendance->id,
            'user_id'       => auth()->id(),
            'status'        => 'pending',
        ]);

        // ② 出勤・退勤の修正内容
        if (
            $attendance->clock_in !== $request->clock_in ||
            $attendance->clock_out !== $request->clock_out
        ) {
            AttendanceCorrectionRequestDetail::create([
                'request_id'  => $correctionRequest->id,
                'target_type' => 'attendance',
                'target_id'   => $attendance->id,
                'before_value'=> json_encode([
                    'clock_in'  => $attendance->clock_in,
                    'clock_out' => $attendance->clock_out,
                ]),
                'after_value' => json_encode([
                    'clock_in'  => $request->clock_in,
                    'clock_out' => $request->clock_out,
                ]),
            ]);
        }

        // ③ 備考
        if ($attendance->note !== $request->note) {
            AttendanceCorrectionRequestDetail::create([
                'request_id'  => $correctionRequest->id,
                'target_type' => 'note',
                'target_id'   => $attendance->id,
                'before_value'=> $attendance->note,
                'after_value' => $request->note,
            ]);
        }

        return redirect()
            ->route('admin.attendance.detail', $attendance->id)
            ->with('message', '修正申請を送信しました');
    }


}
