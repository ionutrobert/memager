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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('debt_id')->references('id')->on('debts')

            ->cascadeOnUpdate()
            ->cascadeOnDelete()
            ->constrained();

            $table->string('act');

            $table->date('data');

            $table->integer('suma');

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
        Schema::dropIfExists('payments');
    }
};
