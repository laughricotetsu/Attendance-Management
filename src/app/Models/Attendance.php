<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Attendance extends Model
    {
        use HasFactory;

        protected $fillable = [
            'user_id',
            'work_date',
            'clock_in',
            'clock_out',
            'status',
            'remarks',
        ];

        protected $casts = [
            'clock_in'  => 'datetime:H:i',
            'clock_out' => 'datetime:H:i',
            ];


        // 勤怠はユーザーに属する
        public function user()
        {
            return $this->belongsTo(User::class);
        }

        /**
         * 勤怠に紐づく休憩
         */
        public function breaks()
        {
            return $this->hasMany(BreakTime::class);
        }

        /**
         * 休憩合計時間 HH:MM
         */
        public function getBreakDurationAttribute()
        {
            if ($this->breaks->isEmpty()) {
                return '00:00';
            }

            $breakMinutes = $this->breaks->sum(function ($break) {
                if ($break->break_start && $break->break_end) {
                    return Carbon::parse($this->work_date . ' ' . $break->break_start)
                        ->diffInMinutes(Carbon::parse($this->work_date . ' ' . $break->break_end));
                }
                return 0;
            });

            $hours = floor($breakMinutes / 60);
            $minutes = $breakMinutes % 60;

            return sprintf('%02d:%02d', $hours, $minutes);
        }

        /**
         * 勤務時間（出勤〜退勤−休憩）HH:MM
         */
        public function getWorkDurationAttribute()
        {
            if (!$this->clock_in || !$this->clock_out) {
                return '-';
            }

            $workMinutes = Carbon::parse($this->clock_in)
                ->diffInMinutes(Carbon::parse($this->clock_out));

            $breakMinutes = $this->breaks->sum(function ($break) {
                if ($break->break_start && $break->break_end) {
                    return Carbon::parse($this->work_date . ' ' . $break->break_start)
                        ->diffInMinutes(Carbon::parse($this->work_date . ' ' . $break->break_end));
                }
                return 0;
            });

            $actualMinutes = $workMinutes - $breakMinutes;
            $hours = floor($actualMinutes / 60);
            $minutes = $actualMinutes % 60;

            return sprintf('%02d:%02d', $hours, $minutes);
        }

        /**
         * 修正申請
         */
        public function correctionRequests()
        {
            return $this->hasMany(AttendanceCorrectionRequest::class);
        }
}