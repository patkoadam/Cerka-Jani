<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchedulesTable extends Migration
{
    public function up()
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('class_group_id'); // Az osztálycsoport ID-je, amelyhez tartozik az órarend
            $table->unsignedBigInteger('teacher_id');     // A tanár, aki ezt a bejegyzést létrehozta
            $table->string('subject');                      // Tantárgy neve
            $table->enum('day_of_week', ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']);
            $table->time('start_time');
            $table->time('end_time');
            $table->string('room')->nullable();
            $table->timestamps();

            $table->foreign('class_group_id')->references('id')->on('class_groups')->onDelete('cascade');
            $table->foreign('teacher_id')->references('id')->on('users')->onDelete('cascade'); // Feltételezzük, hogy a tanár a users táblából származik
        });
    }

    public function down()
    {
        Schema::dropIfExists('schedules');
    }
}

