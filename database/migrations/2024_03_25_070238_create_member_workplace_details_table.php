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
        Schema::create('member_workplace_details', function (Blueprint $table) {
            $table->id();


            $table->foreignId('member_id')->constrained();
            $table->foreignId('workplace_id')->constrained();

            $table->date('data_informatie')->nullable();

            $table->string('tip_informatie')->nullable(); // Revisal, Adeverinta

            $table->date('data_incepere_cim')->nullable(); // Data inceput Contract Individual de Munca
            $table->date('data_incetare_cim')->nullable(); // Data sfarsit Contract Individual de Munca

            $table->string('tip_durata_cim')->nullable(); // Determinata / Nedeterminata
            $table->string('tip_norma_cim')->nullable(); // Norma intreaga / Norma partiala

            $table->string('functie')->nullable();

            $table->integer('salariu_de_baza_lunar_brut')->nullable();
            $table->integer('sporuri_indemnizatii_adaosuri')->nullable();

            $table->string('scan_document')->nullable();

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
        Schema::dropIfExists('member_workplace_details');
    }
};
