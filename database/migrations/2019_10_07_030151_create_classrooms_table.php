<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClassroomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('classrooms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('subject_id')->unsigned();
            $table->bigInteger('teaching_period_id')->unsigned();
            $table->string('slug')->unique();
            $table->string('code')->unique();
            $table->string('title');
            $table->text('description');
            $table->dateTime('start_at')->nullable();
            $table->dateTime('end_at')->nullable();
            $table->bigInteger('created_by')->unsigned();
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
        Schema::dropIfExists('classrooms');
    }
}
