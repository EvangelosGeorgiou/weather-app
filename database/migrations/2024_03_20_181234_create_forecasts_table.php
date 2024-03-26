<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('forecasts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->index()->constrained();
            $table->enum('source', ['openweathermap', 'weatherbit']);
            $table->timestamp('timestamp');
            $table->decimal('temperature');
            $table->decimal('humidity', 5);
            $table->decimal('wind_speed');
            $table->decimal('pressure');
            $table->integer('weather_code');
            $table->string('weather_description');
            $table->timestamps();

            $table->unique(['location_id', 'timestamp','source']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forecasts');
    }
};
