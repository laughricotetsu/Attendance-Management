<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
    {
        use HasFactory, Notifiable;

        // ユーザーの勤怠（1人がたくさん）
        public function attendances()
        {
            return $this->hasMany(Attendance::class);
        }

        // ユーザーの修正申請（一般ユーザー）
        public function attendanceCorrectionRequests()
        {
            return $this->hasMany(AttendanceCorrectionRequest::class);
        }

        // 管理者として承認した申請
        public function approvedRequests()
        {
            return $this->hasMany(
                AttendanceCorrectionRequest::class,
                'approved_by'
            );
        }
    }
