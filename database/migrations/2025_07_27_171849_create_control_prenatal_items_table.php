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
        Schema::create('control_prenatal_items', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('control_id')->unsigned();
            $table->date('fecha');
            $table->integer('edad_gestacional')->nullable();
            $table->decimal('peso', 12, 2)->nullable();
            $table->integer('ta')->nullable();
            $table->integer('au')->nullable();
            $table->string('pres')->nullable();
            $table->integer('fcf')->nullable();
            $table->boolean('mov_fetales')->nullable();
            $table->boolean('du')->nullable();
            $table->boolean('edema')->nullable();
            $table->text('sintomas')->nullable();
            $table->text('observaciones')->nullable();
            $table->foreign('control_id')->references('id')->on('control_prenatal')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('control_prenatal_items');
    }
};
