<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('horoscope_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('birth_date');
            $table->time('birth_time');
            $table->string('gender', 16);
            $table->string('timezone', 64)->default('UTC');
            $table->json('result');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('horoscope_records');
    }
};
