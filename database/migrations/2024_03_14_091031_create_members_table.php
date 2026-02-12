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
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('CNP', 13)->unique();

            $table->string('ci_serie');
            $table->string('ci_numar');
            $table->string('emis_de');
            $table->date('data_emitere');
            $table->date('data_expirare');

            $table->string('nume');
            $table->string('prenume');

            $table->string('cetatenie');
            $table->string('nationalitate');

            $table->string('domiciliu');
            $table->string('oras');
            $table->string('judet');

            $table->string('oras_nastere');
            $table->string('judet_nastere');

            //$table->json('contact_info')->nullable(); // relation manager

            //$table->json('workplace')->nullable(); // relation manager
            /*

Sal / pens
employer_id
Detalii
            perioada ang
            det nedet
            venit
            data adev
            scan adev
            scan revisal
            functia
            data plata sal

            */

            $table->json('alte_info')->nullable();


            $table->string('scan_carte_identitate')->nullable(); // Path to the scan + file name
            $table->string('specimen_semnatura')->nullable(); // Path to the scan + file name

            $table->integer('user_id');

            $table->foreign('user_id')->references('id')->on('users');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
