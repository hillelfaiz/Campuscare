<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('facility_reports', function (Blueprint $table) {
            $table->unsignedBigInteger('reporter_id')->nullable()->after('room_id');
            $table->string('deskripsi_kerusakan')->nullable()->after('nama_fasilitas');
            $table->string('foto')->nullable()->after('deskripsi_kerusakan');
            $table->string('status')->default('pending')->after('foto');
            $table->text('catatan_teknisi')->nullable()->after('status');
            $table->string('assigned_to')->nullable()->after('catatan_teknisi');

            $table->foreign('reporter_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('facility_reports', function (Blueprint $table) {
            $table->dropForeign(['reporter_id']);
            $table->dropColumn([
                'reporter_id',
                'deskripsi_kerusakan',
                'foto',
                'status',
                'catatan_teknisi',
                'assigned_to',
            ]);
        });
    }
};