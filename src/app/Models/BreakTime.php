<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BreakTime extends Model
    {
        use HasFactory;

        protected $table = 'breaks';

        protected $fillable = [
            'attendance_id',
            'break_start',
            'break_end',
        ];

        public function attendance()
        {
            return $this->belongsTo(Attendance::class);
        }

        public function startBreak()
        {
            $attendance = Attendance::where('user_id', auth()->id())
                ->whereDate('work_date', today())
                ->first();

            if ($attendance) {
                BreakTime::create([
                    'attendance_id' => $attendance->id,
                    'start_time' => now(),
                ]);
            }

            return redirect()->route('attendance.index');
        }

    }
