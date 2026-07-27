<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('report_groups', function (Blueprint $table) {
            if (!Schema::hasColumn('report_groups', 'room_id')) {
                $table->foreignId('room_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('rooms')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('report_groups', 'nama_fasilitas')) {
                $table->string('nama_fasilitas')
                    ->nullable()
                    ->after('room_id');
            }

            if (!Schema::hasColumn('report_groups', 'main_title')) {
                $table->string('main_title')
                    ->nullable()
                    ->after('nama_fasilitas');
            }

            if (!Schema::hasColumn('report_groups', 'priority_level')) {
                $table->enum('priority_level', ['low', 'medium', 'high'])
                    ->default('low')
                    ->after('main_title');
            }

            if (!Schema::hasColumn('report_groups', 'report_count')) {
                $table->unsignedInteger('report_count')
                    ->default(1)
                    ->after('priority_level');
            }

            if (!Schema::hasColumn('report_groups', 'unique_reporter_count')) {
                $table->unsignedInteger('unique_reporter_count')
                    ->default(1)
                    ->after('report_count');
            }

            if (!Schema::hasColumn('report_groups', 'status')) {
                $table->enum('status', ['pending', 'in_progress', 'completed'])
                    ->default('pending')
                    ->after('unique_reporter_count');
            }

            if (!Schema::hasColumn('report_groups', 'first_reported_at')) {
                $table->timestamp('first_reported_at')
                    ->nullable()
                    ->after('status');
            }

            if (!Schema::hasColumn('report_groups', 'last_reported_at')) {
                $table->timestamp('last_reported_at')
                    ->nullable()
                    ->after('first_reported_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('report_groups', function (Blueprint $table) {
            if (Schema::hasColumn('report_groups', 'last_reported_at')) {
                $table->dropColumn('last_reported_at');
            }

            if (Schema::hasColumn('report_groups', 'first_reported_at')) {
                $table->dropColumn('first_reported_at');
            }

            if (Schema::hasColumn('report_groups', 'status')) {
                $table->dropColumn('status');
            }

            if (Schema::hasColumn('report_groups', 'unique_reporter_count')) {
                $table->dropColumn('unique_reporter_count');
            }

            if (Schema::hasColumn('report_groups', 'report_count')) {
                $table->dropColumn('report_count');
            }

            if (Schema::hasColumn('report_groups', 'priority_level')) {
                $table->dropColumn('priority_level');
            }

            if (Schema::hasColumn('report_groups', 'main_title')) {
                $table->dropColumn('main_title');
            }

            if (Schema::hasColumn('report_groups', 'nama_fasilitas')) {
                $table->dropColumn('nama_fasilitas');
            }

            if (Schema::hasColumn('report_groups', 'room_id')) {
                $table->dropForeign(['room_id']);
                $table->dropColumn('room_id');
            }
        });
    }
};