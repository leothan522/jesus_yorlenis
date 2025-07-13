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
        Schema::create('pacientes_ant_otros', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('pacientes_id')->unsigned();
            $table->bigInteger('antecedentes_id')->unsigned();
            $table->string('texto')->nullable();
            $table->foreign('pacientes_id')->references('id')->on('pacientes')->cascadeOnDelete();
            $table->foreign('antecedentes_id')->references('id')->on('antecedentes_otros')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pacientes_ant_otros');
    }
};
