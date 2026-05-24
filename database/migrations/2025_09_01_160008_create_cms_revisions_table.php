<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cms_revisions', function (Blueprint $table) {
            $table->id();
            $table->morphs('revisionable'); // polymorphic relation
            $table->json('old_values');
            $table->json('new_values');
            $table->string('action'); // created, updated, deleted
            $table->text('comment')->nullable();
            $table->foreignId('user_id')->constrained('users');
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('created_at');
            $table->index('user_id');
            $table->index('action');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('cms_revisions');
    }
};