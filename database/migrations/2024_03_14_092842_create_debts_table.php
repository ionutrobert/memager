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
        Schema::create('debts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('member_id')->references('id')->on('members')

            ->cascadeOnUpdate()
            ->constrained();

            $table->date('data_acordare');

            $table->integer('suma');

            $table->integer('procent');

            $table->foreignId('user_id')->references('id')->on('users')
            ->cascadeOnUpdate()->constrained();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('debts');
    }
};
