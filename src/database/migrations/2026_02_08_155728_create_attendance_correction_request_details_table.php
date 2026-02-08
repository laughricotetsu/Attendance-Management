<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendanceCorrectionRequestDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendance_correction_request_details', function (Blueprint $table) {
            $table->id();

            // 修正申請ヘッダ
            $table->foreignId('request_id')
                ->constrained('attendance_correction_requests')
                ->cascadeOnDelete();

            // 修正対象の種類（attendance / break）
            $table->string('target_type');

            // 修正対象ID（attendance_id or break_id）
            $table->unsignedBigInteger('target_id')->nullable();

            // 修正前・修正後
            $table->text('before_value')->nullable();
            $table->text('after_value')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendance_correction_request_details');
    }
}
