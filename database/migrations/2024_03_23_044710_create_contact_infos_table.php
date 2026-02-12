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
        Schema::create('contact_infos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('member_id')
            ->references('id')->on('members')
            ->constrained()
            ->cascadeOnDelete()
            ->cascadeOnUpdate();
            $table->string('tip_info');
            $table->string('info');
            $table->timestamps();
            $table->softDeletes();

            $table->foreignId('user_id')
            ->references('id')->on('users')
            ->constrained();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_infos');
    }
};
