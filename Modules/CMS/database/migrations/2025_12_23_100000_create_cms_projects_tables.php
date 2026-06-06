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
        // 1. Projects Table
        Schema::create('cms_projects', function (Blueprint $table) {
            $table->increments('project_id'); // Primary Key

            // Spatie Translatable Fields
            $table->json('project_title'); 
            $table->json('short_description')->nullable();
            $table->json('project_description')->nullable();

            // Icons & Media
            $table->string('icon_class')->nullable();
            $table->string('icon_image')->nullable();
            $table->string('main_image')->nullable();
            $table->text('gallery_images')->nullable(); // Start with TEXT/JSON
            $table->string('breadcrumb_image')->nullable();

            // Scheduling
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            // Status & Flags
            $table->boolean('is_upcoming')->default(0);
            $table->enum('status', ['upcoming', 'in_progress', 'completed'])->default('upcoming');
            $table->boolean('is_active')->default(1);

            // Audit
            $table->integer('created_by')->nullable();
            $table->timestamps();
        });

        // 2. Tasks Table
        Schema::create('cms_tasks', function (Blueprint $table) {
            $table->increments('task_id');
            $table->integer('project_id')->index(); // No Foreign Key Constraint

            // Translatable Fields
            $table->json('task_title');
            $table->json('task_description')->nullable();

            // Task Details
            $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending');
            $table->date('due_date')->nullable();
            
            // Assignment (No FK)
            $table->integer('assigned_to')->nullable(); // Can refer to project_member_id or user_id

            $table->timestamps();
        });

        // 3. Project Members Table
        Schema::create('cms_project_members', function (Blueprint $table) {
            $table->increments('member_id');
            $table->integer('project_id')->index(); // No Foreign Key Constraint

            // Member Details
            $table->integer('user_id')->nullable(); // If linking to existing system User
            $table->json('name')->nullable(); // Translatable name if manually added
            $table->json('role')->nullable(); // Translatable role (e.g. 'Manager' in EN/AR)
            $table->string('member_image')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cms_project_members');
        Schema::dropIfExists('cms_tasks');
        Schema::dropIfExists('cms_projects');
    }
};
