<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeacheablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teachables', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('classroom_id')->unsigned();
            $table->string('teachable_type');
            $table->bigInteger('teachable_id')->unsigned();
            $table->bigInteger('created_by')->unsigned();
            $table->dateTime('available_at')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->double('pass_threshold')->default(0);
            $table->double('final_grade_weight')->default(0);
            $table->integer('max_attempts_acount')->default(0);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('teacheables');
    }
}
