<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendanceCorrectionRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendance_correction_requests', function (Blueprint $table) {
            $table->id();

            // 対象の勤怠
            $table->foreignId('attendance_id')
                ->constrained()
                ->cascadeOnDelete();

            // 申請者（一般ユーザー）
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // 申請ステータス
            $table->string('status'); // pending / approved / rejected

            // 申請理由
            $table->text('reason');

            // 承認者（管理者）
            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // 承認日時
            $table->timestamp('approved_at')->nullable();

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
        Schema::dropIfExists('attendance_correction_requests');
    }
}
