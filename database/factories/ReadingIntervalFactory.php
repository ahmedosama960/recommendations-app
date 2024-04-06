<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class ReadingIntervalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $users = User::all();
        $books = Book::all();
        $data = [];
        foreach($users as $user){
            $user_id = $user->id;
            foreach ($books as $book){
            $validate = random_int(0,2);

            if ($validate){
            $start_page = random_int(10,50);
            $end_page = random_int(50,100);
                    $data[] = [
                        "user_id"=>$user_id,
                        "book_id"=>$book->id,
                        "start_page"=>$start_page,
                        "end_page"=>$end_page,
                    ];
                }
            }
        }

        return $data;
    }

}
