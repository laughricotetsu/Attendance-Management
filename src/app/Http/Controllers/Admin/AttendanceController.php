<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;


class AttendanceController extends Controller
{
    public function index()
    {
            if (auth()->user()->role !== 'admin') {
        abort(403);
        }


        // 勤怠 + ユーザーを一緒に取得
        $attendances = Attendance::with('user')
            ->orderBy('work_date', 'desc')
            ->get();

        return view('admin.attendance.list', compact('attendances'));
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
            // 管理者：直接修正
            $attendance->update([
                'clock_in'  => $request->clock_in,
                'clock_out' => $request->clock_out,
                'note'      => $request->note,
            ]);

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
