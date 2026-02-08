<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceCorrectionRequestDetail extends Model
    {
        use HasFactory;

        protected $fillable = [
            'request_id',
            'target_type',
            'target_id',
            'before_value',
            'after_value',
        ];

        public function request()
        {
            return $this->belongsTo(
                AttendanceCorrectionRequest::class,
                'request_id'
            );
        }
    }
