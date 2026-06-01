<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use App\Models\LmsLibraryBook;
use App\Helpers\TemplateHelper;
use App\Models\StudentGroup;
use App\Models\AcademicGroup;
use App\Models\Employee;
use App\Models\TeacherSubject;
use App\Observers\StudentGroupObserver;
use App\Observers\EmployeeObserver;
use App\Observers\TeacherSubjectObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Older MySQL/MariaDB index key length fix (utf8mb4, InnoDB)
        Schema::defaultStringLength(191);

        // HTTPS majburiy qilish (production muhitda)
        if($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // StudentGroup yaratilganda avtomatik jurnal yaratish
        StudentGroup::observe(StudentGroupObserver::class);

        // AcademicGroup ham ishlatilsa
        AcademicGroup::observe(StudentGroupObserver::class);

        // Employee yaratilganda avtomatik teacher yozuvi yaratish
        Employee::observe(EmployeeObserver::class);

        // TeacherSubject yaratilganda avtomatik jurnal yaratish
        TeacherSubject::observe(TeacherSubjectObserver::class);

        // Route model binding for library books
        Route::model('book', LmsLibraryBook::class);

        // Share template helper with all views (skip during CLI/artisan commands)
        if (!$this->app->runningInConsole()) {
            View::share('activeTemplate', TemplateHelper::getActiveTemplate());
        }

        // Blade directive for template-specific content
        Blade::directive('template', function ($expression) {
            return "<?php if(\\App\\Helpers\\TemplateHelper::isTemplate({$expression})): ?>";
        });

        Blade::directive('endtemplate', function () {
            return "<?php endif; ?>";
        });
    }
}
