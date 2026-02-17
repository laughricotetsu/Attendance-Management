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
            'note',
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

        // 休憩は複数
        public function breaks()
        {
            return $this->hasMany(BreakTime::class);
        }

        public function getTotalBreakTimeAttribute()
        {
            $totalSeconds = 0;

            foreach ($this->breaks as $break) {
                if ($break->break_start && $break->break_end) {
                    $start = strtotime($break->break_start);
                    $end   = strtotime($break->break_end);

                    $totalSeconds += ($end - $start);
                }
            }

            $hours = floor($totalSeconds / 3600);
            $minutes = floor(($totalSeconds % 3600) / 60);

            return sprintf('%02d:%02d', $hours, $minutes);
        }

        public function getWorkTimeAttribute()
        {
            if (!$this->clock_in || !$this->clock_out) {
                return '';
            }

            $workSeconds =
                strtotime($this->clock_out) - strtotime($this->clock_in)
                - $this->breaks->sum(fn ($b) =>
                    strtotime($b->break_end) - strtotime($b->break_start)
                );

            return gmdate('H:i', $workSeconds);
        }


        // 修正申請
        public function correctionRequests()
        {
            return $this->hasMany(AttendanceCorrectionRequest::class);
        }

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

        public function getBreakDurationAttribute()
        {
            if ($this->breaks->isEmpty()) {
                return '-';
            }

            $breakMinutes = $this->breaks->sum(function ($break) {

                if ($break->break_start && $break->break_end) {

                    return \Carbon\Carbon::parse($this->work_date . ' ' . $break->break_start)
                        ->diffInMinutes(
                            \Carbon\Carbon::parse($this->work_date . ' ' . $break->break_end)
                        );
                }

                return 0;
            });

            if ($breakMinutes === 0) {
                return '00:00';
            }

            $hours = floor($breakMinutes / 60);
            $minutes = $breakMinutes % 60;

            return sprintf('%02d:%02d', $hours, $minutes);
        }


    }
