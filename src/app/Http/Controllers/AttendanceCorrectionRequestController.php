<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\BreakTime;
use Illuminate\Support\Facades\DB;
use App\Models\AttendanceCorrectionRequest;
use App\Models\AttendanceCorrectionRequestDetail;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AttendanceCorrectionStoreRequest;

class AttendanceCorrectionRequestController extends Controller
{

    /**
     * 申請一覧（管理者）
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        $status = $request->status ?? 'pending';

        $query = AttendanceCorrectionRequest::with(['user','attendance'])
            ->where('status',$status)
            ->latest();

        // 一般ユーザーは自分の申請のみ
        if ($user->role !== 'admin') {
            $query->where('user_id',$user->id);
        }

        $requests = $query->get();

        return view(
            'stamp_correction_request.list',
            compact('requests','status')
        );
    }
    /**
     * 修正申請（ユーザー）
     */
    public function store(AttendanceCorrectionStoreRequest $request, $attendanceId)
    {
        $attendance = Attendance::with('breaks')->findOrFail($attendanceId);

        DB::beginTransaction();

        try {

            // 修正申請
            $correctionRequest = AttendanceCorrectionRequest::create([
                'attendance_id' => $attendance->id,
                'user_id' => Auth::id(),
                'status' => 'pending',
                'reason' => $request->remarks,
            ]);

            $details = [];

            // 出勤
            if ($request->clock_in != optional($attendance->clock_in)->format('H:i')) {

                $details[] = [
                    'request_id' => $correctionRequest->id,
                    'target_type' => 'clock_in',
                    'target_id' => $attendance->id,
                    'before_value' => $attendance->clock_in,
                    'after_value' => $request->clock_in,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // 退勤
            if ($request->clock_out != optional($attendance->clock_out)->format('H:i')) {

                $details[] = [
                    'request_id' => $correctionRequest->id,
                    'target_type' => 'clock_out',
                    'target_id' => $attendance->id,
                    'before_value' => $attendance->clock_out,
                    'after_value' => $request->clock_out,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            // 休憩
            foreach ($attendance->breaks as $index => $break) {
                $index2=$break->id;
                $start = $request->breaks[$index2]['break_start'] ?? null;
                $end = $request->breaks[$index2]['break_end'] ?? null;

                if ($start != optional($break->break_start)->format('H:i')) {

                    $details[] = [
                        'request_id' => $correctionRequest->id,
                        'target_type' => 'break_start',
                        'target_id' => $break->id,
                        'before_value' => $break->break_start,
                        'after_value' => $start,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                if ($end != optional($break->break_end)->format('H:i')) {

                    $details[] = [
                        'request_id' => $correctionRequest->id,
                        'target_type' => 'break_end',
                        'target_id' => $break->id,
                        'before_value' => $break->break_end,
                        'after_value' => $end,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            // 備考
            if ($request->remarks != $attendance->remarks) {

                $details[] = [
                    'request_id' => $correctionRequest->id,
                    'target_type' => 'remarks',
                    'target_id' => $attendance->id,
                    'before_value' => $attendance->remarks,
                    'after_value' => $request->remarks,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            AttendanceCorrectionRequestDetail::insert($details);

            DB::commit();

            return redirect()->route('attendance.list')
                ->with('success', '修正申請を送信しました');

        } catch (\Exception $e) {

            DB::rollBack();
            throw $e;
        }
    }
    /**
     * 承認画面
     */
    public function show($id)
    {
        $request = AttendanceCorrectionRequest::with([
            'user',
            'details',
            'attendance.breaks'
        ])->findOrFail($id);

        $attendance = $request->attendance;

        return view(
            'admin.stamp_correction_request.approve',
            compact('request','attendance')
        );
    }

        public function approve($id)
    {
        $request = AttendanceCorrectionRequest::with([
            'user',
            'attendance',
            'details'
        ])->findOrFail($id);

        return view(
            'admin.stamp_correction_request.approve',
            compact('request')
        );
    }

    /**
     * 承認処理
     */
    public function approveUpdate($requestId)
    {
        $request = AttendanceCorrectionRequest::with('details')->findOrFail($requestId);

        DB::transaction(function () use ($request) {

            foreach ($request->details as $detail) {

                switch ($detail->target_type) {

                    case 'clock_in':
                        Attendance::where('id', $detail->target_id)
                            ->update([
                                'clock_in' => $detail->after_value
                            ]);
                        break;

                    case 'clock_out':
                        Attendance::where('id', $detail->target_id)
                            ->update([
                                'clock_out' => $detail->after_value
                            ]);
                        break;

                    case 'break_start':
                        BreakTime::where('id', $detail->target_id)
                            ->update([
                                'break_start' => $detail->after_value
                            ]);
                        break;

                    case 'break_end':
                        BreakTime::where('id', $detail->target_id)
                            ->update([
                                'break_end' => $detail->after_value
                            ]);
                        break;

                    case 'remarks':
                        Attendance::where('id', $detail->target_id)
                            ->update([
                                'remarks' => $detail->after_value
                            ]);
                        break;
                }
            }

            $request->update([
                'status' => 'approved'
            ]);
        });

        return back();
    }
}
