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
        Schema::create('workflow_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('workflow_id')->index();
            $table->unsignedBigInteger('workflow_transition_id')->index();
            $table->string('assignable_type')->nullable()->comment('User, Role, Team, etc.');
            $table->string('assignable_id')->nullable();
            $table->string('assignment_rule')->default('explicit')->comment('explicit, creator, model_field');
            $table->string('assignment_value')->nullable()->comment('the field name or specific ID');
            $table->boolean('is_primary')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_assignments');
    }
};
