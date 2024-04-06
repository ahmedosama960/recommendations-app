<?php

namespace App\Providers;

use App\Models\Book;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

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
        //
        Validator::extend('book_is_active', function ($attribute, $value, $parameters, $validator) {
            $inputs = $validator->getData();
            $book_id = $inputs['book_id'];
            $result = Book::findorFail($book_id);
            if ($result->is_active == 1 ) return true;
            return false;
        });
    }
}
