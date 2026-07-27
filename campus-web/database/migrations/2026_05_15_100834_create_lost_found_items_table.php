<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lost_found_items', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['found', 'lost']);
            $table->string('name');
            $table->string('category');
            $table->string('location')->nullable();
            $table->text('description')->nullable();
            $table->string('photo');
            $table->string('reporter_id_photo')->nullable();
            $table->foreignId('reported_by')->constrained('users')->cascadeOnDelete();
            $table->string('status')->default('pending_publish'); 
            $table->string('claim_name')->nullable();
            $table->string('claim_id_type')->nullable();
            $table->string('claim_id_number')->nullable();
            $table->string('claim_proof_photo')->nullable();
            $table->string('claim_handover_photo')->nullable();
            $table->json('claim_history')->nullable();
            $table->dateTime('scheduled_at')->nullable();
            $table->foreignId('claimed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('claimed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lost_found_items');
    }
};