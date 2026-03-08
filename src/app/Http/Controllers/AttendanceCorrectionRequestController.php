<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\AttendanceCorrectionRequest;
use App\Models\AttendanceCorrectionRequestDetail;
use Illuminate\Support\Facades\Auth;

class AttendanceCorrectionRequestController extends Controller
{

    /**
     * 申請一覧（管理者）
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $status = $request->status ?? 'pending';

        $query = AttendanceCorrectionRequest::with([
            'user',
            'attendance'
        ])
        ->where('status',$status)
        ->orderBy('created_at','desc');

        if(!$user->is_admin){
            $query->where('user_id',$user->id);
        }

        $requests = $query->get();

        return view(
            'stamp_correction_request.list',
            compact('requests')
        );
    }

    /**
     * 修正申請（ユーザー）
     */
    public function store(Request $request, $attendanceId)
    {
        $request->validate([
            'remarks' => 'required|string|max:255',
        ]);

        $attendance = Attendance::with('breaks')->findOrFail($attendanceId);

        $exists = AttendanceCorrectionRequest::where('attendance_id',$attendanceId)
            ->where('status','pending')
            ->exists();

        if($exists){
            return redirect()->back()
                ->with('error','既に修正申請中です');
        }

        // 申請ヘッダー作成
        $correctionRequest = AttendanceCorrectionRequest::create([
            'attendance_id' => $attendance->id,
            'user_id' => Auth::id(),
            'status' => 'pending',
            'reason' => $request->remarks,
        ]);

        $details = [];

        // ======================
        // 出勤
        // ======================
        if ($request->clock_in != $attendance->clock_in) {

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

        // ======================
        // 退勤
        // ======================
        if ($request->clock_out != $attendance->clock_out) {

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

        // ======================
        // 備考
        // ======================
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

        // ======================
        // 休憩
        // ======================
        if ($request->breaks) {

            foreach ($request->breaks as $index => $breakData) {

                $break = $attendance->breaks[$index] ?? null;

                if ($break) {

                    if ($breakData['break_start'] != $break->break_start) {

                        $details[] = [
                            'request_id' => $correctionRequest->id,
                            'target_type' => 'break_start',
                            'target_id' => $break->id,
                            'before_value' => $break->break_start,
                            'after_value' => $breakData['break_start'],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }

                    if ($breakData['break_end'] != $break->break_end) {

                        $details[] = [
                            'request_id' => $correctionRequest->id,
                            'target_type' => 'break_end',
                            'target_id' => $break->id,
                            'before_value' => $break->break_end,
                            'after_value' => $breakData['break_end'],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }

                }
            }
        }

        // 詳細保存
        if (!empty($details)) {
            AttendanceCorrectionRequestDetail::insert($details);
        }

        return redirect()->back()->with('success','修正申請を送信しました');
    }


    /**
     * 承認画面
     */
    public function show($id)
    {
        $correctionRequest = AttendanceCorrectionRequest::with([
            'user',
            'attendance',
            'details'
        ])->findOrFail($id);

        return view(
            'admin.stamp_correction_request.approve',
            compact('correctionRequest')
        );
    }


    /**
     * 承認処理
     */
    public function approve($id)
    {
        $correctionRequest = AttendanceCorrectionRequest::with('details')
            ->findOrFail($id);

        foreach ($correctionRequest->details as $detail) {

            switch ($detail->target_type) {

                case 'clock_in':
                    Attendance::where('id',$detail->target_id)
                        ->update(['clock_in'=>$detail->after_value]);
                    break;

                case 'clock_out':
                    Attendance::where('id',$detail->target_id)
                        ->update(['clock_out'=>$detail->after_value]);
                    break;

                case 'remarks':
                    Attendance::where('id',$detail->target_id)
                        ->update(['remarks'=>$detail->after_value]);
                    break;

                case 'break_start':
                    BreakTime::where('id',$detail->target_id)
                        ->update(['break_start'=>$detail->after_value]);
                    break;

                case 'break_end':
                    BreakTime::where('id',$detail->target_id)
                        ->update(['break_end'=>$detail->after_value]);
                    break;
            }
        }

        $correctionRequest->update([
            'status' => 'approved'
        ]);

        return redirect()
            ->route('admin.correction.request.list')
            ->with('success','申請を承認しました');
    }

}