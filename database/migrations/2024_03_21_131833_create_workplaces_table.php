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
        Schema::create('workplaces', function (Blueprint $table) {
            $table->id();
            $table->string('employer');
            $table->string('CUI')->nullable();
            $table->string('reg_com')->nullable();
            $table->string('adresa');
            $table->string('oras');
            $table->string('judet');
            $table->json('contact')->nullable(); // tel fax site etc
            $table->json('info')->nullable(); // director / res umane specimene semnatura scans

            $table->foreignId('user_id')->constrained();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workplaces');
    }
};
