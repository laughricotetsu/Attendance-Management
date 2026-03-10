<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceCorrectionStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'clock_in' => ['nullable'],
            'clock_out' => ['nullable'],
            'remarks' => ['required'],
        ];
    }

    public function messages()
    {
        return [
            'remarks.required' => '備考を記入してください',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            $clockIn = $this->clock_in;
            $clockOut = $this->clock_out;

            /*
            |--------------------------------------------------------------------------
            | 出勤・退勤チェック
            |--------------------------------------------------------------------------
            */

            if ($clockIn && $clockOut && $clockIn >= $clockOut) {

                $validator->errors()->add(
                    'clock_in',
                    '出勤時間もしくは退勤時間が不適切な値です'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | 休憩時間チェック
            |--------------------------------------------------------------------------
            */

            if ($this->breaks) {

                foreach ($this->breaks as $index => $break) {

                    $breakStart = $break['break_start'] ?? null;
                    $breakEnd = $break['break_end'] ?? null;

                    // 休憩開始 < 出勤
                    if ($breakStart && $clockIn && $breakStart < $clockIn) {

                        $validator->errors()->add(
                            'break_start',
                            '休憩時間が不適切な値です'
                        );
                    }

                    // 休憩開始 > 退勤
                    if ($breakStart && $clockOut && $breakStart > $clockOut) {

                        $validator->errors()->add(
                            'break_start',
                            '休憩時間が不適切な値です'
                        );
                    }

                    // 休憩終了 > 退勤
                    if ($breakEnd && $clockOut && $breakEnd > $clockOut) {

                        $validator->errors()->add(
                            'break_end',
                            '休憩時間もしくは退勤時間が不適切な値です'
                        );
                    }

                    // 休憩終了 < 休憩開始
                    if ($breakStart && $breakEnd && $breakEnd < $breakStart) {

                        $validator->errors()->add(
                            'break_end',
                            '休憩時間が不適切な値です'
                        );
                    }
                }
            }

        });
    }
}