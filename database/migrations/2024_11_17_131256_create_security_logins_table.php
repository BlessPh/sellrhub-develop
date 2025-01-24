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
        Schema::create('security_logins', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address');
            $table->string('device_info');
            $table->dateTime('login_date');
            $table->dateTime('logout_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('security_logins');
    }
};
