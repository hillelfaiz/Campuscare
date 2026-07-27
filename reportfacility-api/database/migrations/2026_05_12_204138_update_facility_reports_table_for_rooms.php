<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('facility_reports', function (Blueprint $table) {
            // Drop location column if exists
            if (Schema::hasColumn('facility_reports', 'location')) {
                $table->dropColumn('location');
            }
            if (Schema::hasColumn('facility_reports', 'asset_kode')) {
                $table->dropColumn('asset_kode'); // the user might have had this previously based on the model
            }
            
            // Add new columns
            $table->foreignId('room_id')->nullable()->constrained('rooms')->nullOnDelete();
            $table->string('nama_fasilitas')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('facility_reports', function (Blueprint $table) {
            $table->dropForeign(['room_id']);
            $table->dropColumn(['room_id', 'nama_fasilitas']);
            $table->string('location')->nullable();
        });
    }
};
