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
            'start_time',
            'end_time',
            'status',
            'note',
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
            if (!$this->start_time || !$this->end_time) {
                return '';
            }

            $workSeconds =
                strtotime($this->end_time) - strtotime($this->start_time)
                - $this->breaks->sum(fn($b) =>
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
