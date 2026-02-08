<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();

            // ユーザー紐付け
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // 勤務日
            $table->date('work_date');

            // 出勤・退勤
            $table->time('clock_in')->nullable();
            $table->time('clock_out')->nullable();

            // 勤務ステータス
            $table->string('status');

            // 備考（修正申請で必須）
            $table->text('remarks')->nullable();

            $table->timestamps();

            // 1ユーザー1日1勤怠を保証
            $table->unique(['user_id', 'work_date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendances');
    }
}
