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
        Schema::create('workflow_transitions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('workflow_id')->index();
            $table->unsignedBigInteger('from_step_id')->index()->nullable();
            $table->unsignedBigInteger('to_step_id')->index();
            $table->string('action_name');
            $table->string('action_code')->index();
            $table->string('permission')->nullable();
            $table->json('notification_rules')->nullable();
            $table->text('custom_message')->nullable();
            $table->json('field_updates')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_transitions');
    }
};
