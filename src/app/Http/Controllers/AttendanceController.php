<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use Carbon\Carbon;
use App\Models\BreakTime;
use App\Models\AttendanceCorrectionRequest;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{


    public function index()
    {
        $attendance = Attendance::where('user_id', auth()->id())
            ->whereDate('work_date', today())
            ->first();

        $status = 'off';

        if (!$attendance || !$attendance->clock_in) {
            $status = 'off';
        } elseif ($attendance->clock_out) {
            $status = 'finished';
        } elseif ($attendance->breaks()->whereNull('break_end')->exists()) {
            $status = 'break';
        } else {
            $status = 'working';
        }


        return view('attendance.index', compact('attendance', 'status'));
    }

    public function startWork()
    {
        $today = Carbon::today();

        $attendance = Attendance::firstOrCreate(
            [
                'user_id' => auth()->id(),
                'work_date' => $today,
            ],
            [
                'status' => 'working'
            ]
        );

        $attendance->update([
            'clock_in' => now(),
            'status' => 'working'
        ]);

        return redirect()->route('attendance.index');
    }

    public function startBreak()
    {
        $attendance = Attendance::where('user_id', auth()->id())
            ->whereDate('work_date', today())
            ->first();

        if ($attendance) {
            BreakTime::create([
                'attendance_id' => $attendance->id,
                'break_start' => now(),
            ]);
        }

        return redirect()->route('attendance.index');
    }

    public function endBreak()
    {
        $attendance = Attendance::where('user_id', auth()->id())
            ->whereDate('work_date', today())
            ->first();

        if ($attendance) {

            $latestBreak = BreakTime::where('attendance_id', $attendance->id)
                ->whereNull('break_end')
                ->latest()
                ->first();

            if ($latestBreak) {
                $latestBreak->break_end = now();
                $latestBreak->save();
            }
        }

        return redirect()->route('attendance.index');
    }

    public function finish()
    {
        $attendance = Attendance::where('user_id', auth()->id())
            ->whereDate('work_date', today())
            ->first();

        if ($attendance && !$attendance->clock_out) {
            $attendance->update([
                'clock_out' => now(),
                'status' => 'finished'
            ]);
        }

        return redirect()->route('attendance.index');
    }

    public function list(Request $request)
    {
        // 月を取得（なければ今月）
        $currentMonth = $request->month
            ? Carbon::parse($request->month)
            : Carbon::now();

        // 月の最初と最後
        $startOfMonth = $currentMonth->copy()->startOfMonth();
        $endOfMonth   = $currentMonth->copy()->endOfMonth();

        // その月の勤怠取得
        $attendances = Attendance::with('breaks')
            ->where('user_id', auth()->id())
            ->whereBetween('work_date', [$startOfMonth, $endOfMonth])
            ->orderBy('work_date', 'asc')
            ->get();

        return view('attendance.list', compact('attendances', 'currentMonth'));
    }

    public function detail($id)
    {
        $attendance = Attendance::with(['user','breaks'])
            ->findOrFail($id);

        // 管理者判定
        $isAdmin = Auth::user()->is_admin ?? false;

        // 承認待ち申請（details付き）
        $pendingRequest = AttendanceCorrectionRequest::with('details')
            ->where('attendance_id', $id)
            ->where('status', 'pending')
            ->first();

        // 管理者用申請データ
        $correctionRequest = null;

        if ($isAdmin) {
            $correctionRequest = AttendanceCorrectionRequest::with('details')
                ->where('attendance_id',$id)
                ->where('status','pending')
                ->first();
        }

        return view(
            'attendance.detail',
            compact(
                'attendance',
                'isAdmin',
                'pendingRequest',
                'correctionRequest'
            )
        );
    }

    public function update(AttendanceCorrectionStoreRequest $request, Attendance $attendance)
    {

        // 他人の勤怠編集禁止
        if ($attendance->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'clock_in'  => 'nullable|date_format:H:i',
            'clock_out' => 'nullable|date_format:H:i',
            'breaks.*.break_start' => 'nullable|date_format:H:i',
            'breaks.*.break_end'   => 'nullable|date_format:H:i',
            'remarks' => 'nullable|string|max:255',
        ]);

        // 出退勤更新
        $attendance->update([
            'clock_in'  => $request->clock_in,
            'clock_out' => $request->clock_out,
            'remarks'   => $request->remarks,
        ]);

        // 休憩更新
        foreach ($request->breaks ?? [] as $breakId => $breakData) {

            $start = $breakData['break_start'] ?? null;
            $end   = $breakData['break_end'] ?? null;

            // startが無ければ何もしない
            if (!$start) {
                continue;
            }

            if ($breakId === 'new') {

                $attendance->breaks()->create([
                    'break_start' => $start,
                    'break_end'   => $end,
                ]);

            } else {

                $break = $attendance->breaks()->find($breakId);

                if ($break) {
                    $break->update([
                        'break_start' => $start,
                        'break_end'   => $end,
                    ]);
                }
            }
        }
        return back()->with('success', '更新しました');
    }


}
