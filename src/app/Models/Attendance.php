<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
            $seconds = $this->breaks->sum(function ($break) {
                return strtotime($break->break_end) - strtotime($break->break_start);
            });

            return gmdate('H:i', $seconds);
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
    }
