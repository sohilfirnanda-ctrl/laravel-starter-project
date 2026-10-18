<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_servis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kendaraan_id')
                ->constrained('kendaraans')
                ->cascadeOnDelete();
            $table->date('tanggal_servis');
            $table->text('keluhan');
            $table->text('tindakan')->nullable();
            $table->unsignedInteger('kilometer')->nullable();
            $table->decimal('biaya', 12, 2)->default(0);
            $table->string('status')->default('Menunggu');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_servis');
    }
};