<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cms_forms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->json('fields'); // form field definitions
            $table->json('settings'); // notifications, redirects, etc.
            $table->string('submit_button_text')->default('Submit');
            $table->string('success_message')->nullable();
            $table->string('email_to')->nullable();
            $table->boolean('save_submissions')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('slug');
            $table->index('is_active');
        });
        
        Schema::create('cms_form_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->constrained('cms_forms')->onDelete('cascade');
            $table->json('data');
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->enum('status', ['new', 'read', 'processed', 'spam'])->default('new');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('form_id');
            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('cms_form_submissions');
        Schema::dropIfExists('cms_forms');
    }
};