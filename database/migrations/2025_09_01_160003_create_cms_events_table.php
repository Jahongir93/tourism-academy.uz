<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cms_events', function (Blueprint $table) {
            $table->id();
            $table->string('title_uz');
            $table->string('title_ru')->nullable();
            $table->string('title_en')->nullable();
            $table->string('slug')->unique();
            $table->text('description_uz');
            $table->text('description_ru')->nullable();
            $table->text('description_en')->nullable();
            $table->longText('content_uz')->nullable();
            $table->longText('content_ru')->nullable();
            $table->longText('content_en')->nullable();
            $table->string('featured_image')->nullable();
            $table->json('gallery')->nullable();
            $table->dateTime('start_date');
            $table->dateTime('end_date')->nullable();
            $table->string('location')->nullable();
            $table->string('venue')->nullable();
            $table->json('coordinates')->nullable(); // {lat, lng}
            $table->enum('type', ['conference', 'seminar', 'workshop', 'meeting', 'ceremony', 'other'])->default('other');
            $table->enum('status', ['upcoming', 'ongoing', 'completed', 'cancelled'])->default('upcoming');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_online')->default(false);
            $table->string('online_link')->nullable();
            $table->boolean('requires_registration')->default(false);
            $table->integer('max_participants')->nullable();
            $table->integer('registered_count')->default(0);
            $table->json('organizers')->nullable();
            $table->json('speakers')->nullable();
            $table->json('agenda')->nullable();
            $table->json('attachments')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->integer('views_count')->default(0);
            $table->timestamps();
            
            $table->index('slug');
            $table->index('status');
            $table->index('start_date');
            $table->index('type');
            $table->index('is_featured');
        });
        
        Schema::create('cms_event_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('cms_events')->onDelete('cascade');
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('organization')->nullable();
            $table->string('position')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending');
            $table->string('confirmation_code')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();
            
            $table->index('event_id');
            $table->index('email');
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('cms_event_registrations');
        Schema::dropIfExists('cms_events');
    }
};