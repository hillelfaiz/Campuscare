<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_groups', function (Blueprint $table) {
            $table->id();

            $table->foreignId('room_id')
                ->nullable()
                ->constrained('rooms')
                ->nullOnDelete();

            $table->string('nama_fasilitas')->nullable();
            $table->string('main_title')->nullable();

            $table->enum('priority_level', ['low', 'medium', 'high'])
                ->default('low');

            $table->unsignedInteger('report_count')->default(1);
            $table->unsignedInteger('unique_reporter_count')->default(1);

            $table->enum('status', ['pending', 'in_progress', 'completed'])
                ->default('pending');

            $table->timestamp('first_reported_at')->nullable();
            $table->timestamp('last_reported_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_groups');
    }
};