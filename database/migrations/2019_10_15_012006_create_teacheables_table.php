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
        Schema::create('teacheables', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('classroom_id')->unsigned();
            $table->string('teacheable_type');
            $table->bigInteger('teacheable_id')->unsigned();
            $table->bigInteger('created_by')->unsigned();
            $table->dateTime('available_at')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->double('pass_threshold');
            $table->double('final_grade_weight');
            $table->integer('max_attempts_acount');

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
