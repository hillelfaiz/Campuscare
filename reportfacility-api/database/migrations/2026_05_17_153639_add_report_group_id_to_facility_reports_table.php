<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('facility_reports', function (Blueprint $table) {
            if (!Schema::hasColumn('facility_reports', 'report_group_id')) {
                $table->foreignId('report_group_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('report_groups')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('facility_reports', 'reporter_id')) {
                $table->foreignId('reporter_id')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('facility_reports', 'deskripsi_kerusakan')) {
                $table->text('deskripsi_kerusakan')->nullable();
            }

            if (!Schema::hasColumn('facility_reports', 'foto')) {
                $table->string('foto')->nullable();
            }

            if (!Schema::hasColumn('facility_reports', 'status')) {
                $table->enum('status', ['pending', 'in_progress', 'completed'])
                    ->default('pending');
            }

            if (!Schema::hasColumn('facility_reports', 'catatan_teknisi')) {
                $table->text('catatan_teknisi')->nullable();
            }

            if (!Schema::hasColumn('facility_reports', 'assigned_to')) {
                $table->string('assigned_to')->nullable();
            }

            if (!Schema::hasColumn('facility_reports', 'similarity_score')) {
                $table->unsignedTinyInteger('similarity_score')
                    ->nullable()
                    ->comment('Score kemiripan dengan report group');
            }
        });
    }

    public function down(): void
    {
        Schema::table('facility_reports', function (Blueprint $table) {
            if (Schema::hasColumn('facility_reports', 'report_group_id')) {
                $table->dropForeign(['report_group_id']);
                $table->dropColumn('report_group_id');
            }

            if (Schema::hasColumn('facility_reports', 'similarity_score')) {
                $table->dropColumn('similarity_score');
            }
        });
    }
};