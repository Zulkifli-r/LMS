<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGradesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('gradeable_id')->unsigned();
            $table->string('gradeable_type');
            $table->string('grading_method')->default('manual');
            $table->double('grade')->default(0);
            $table->text('comments')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->bigInteger('graded_by')->unsigned();
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
        Schema::dropIfExists('grades');
    }
}
