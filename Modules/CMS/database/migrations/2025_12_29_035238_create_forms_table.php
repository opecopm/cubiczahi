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
        Schema::create('cms_forms', function (Blueprint $table) {
            $table->id();
            $table->json('title'); // Translatable
            $table->json('description')->nullable(); // Translatable
            $table->string('status')->default('active'); // active, inactive
            
            // Notification Settings
            $table->json('mail_settings')->nullable(); // SMTP details if custom
            $table->json('notification_emails')->nullable(); // Admin emails
            $table->json('email_template')->nullable(); // Subject, Body
            
            // Auto Response
            $table->boolean('auto_responder')->default(false);
            $table->json('auto_responder_template')->nullable(); // Subject, Body

            // Security
            $table->boolean('use_captcha')->default(false);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cms_forms');
    }
};
