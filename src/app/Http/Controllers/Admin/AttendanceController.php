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

        $attendances = Attendance::with(['user', 'breaks'])
            ->orderBy('work_date', 'desc')
            ->get();

            foreach ($attendances as $attendance) {

            if ($attendance->clock_in && $attendance->clock_out) {

                $workSeconds = strtotime($attendance->clock_out) - strtotime($attendance->clock_in);

                $breakSeconds = 0;

                foreach ($attendance->breaks as $break) {

                    if ($break->break_start && $break->break_end) {

                        $start = strtotime($attendance->work_date . ' ' . $break->break_start);
                        $end   = strtotime($attendance->work_date . ' ' . $break->break_end);

                        $breakSeconds += ($end - $start);
                    }
                }

                $attendance->break_duration = gmdate('H:i', $breakSeconds);

                $totalSeconds = $workSeconds - $breakSeconds;

                $attendance->work_duration = gmdate('H:i', $totalSeconds);

            } else {
                $attendance->work_duration = '-';
                $attendance->break_duration = '-';
            }
        }

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

            $attendance->update([
                'clock_in'  => $request->clock_in,
                'clock_out' => $request->clock_out,
                'note'      => $request->note,
            ]);

            // 🔥 休憩も更新
            if ($request->has('breaks')) {

                foreach ($attendance->breaks as $index => $break) {

                    if (isset($request->breaks[$index])) {

                        $break->update([
                            'break_start' => $request->breaks[$index]['break_start'],
                            'break_end'   => $request->breaks[$index]['break_end'],
                        ]);
                    }
                }
            }

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
