<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGcEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gc_events', function (Blueprint $table) {
            $table->increments('id');

            # Relationships.
            $table->unsignedInteger('calendar_id');
            $table->foreign('calendar_id')
                ->references('id')->on('calendars')
                ->onDelete('cascade');

            # Data.
            $table->string('google_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('all_day')->default(false);

            # Timestamps.
            $table->datetime('started_at');
            $table->datetime('ended_at');
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
        Schema::dropIfExists('gc_events');
    }
}
