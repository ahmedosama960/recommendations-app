<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        DB::connection(env('DB_CONNECTION'))->table('reading_intervals')->truncate();
        DB::connection(env('DB_CONNECTION'))->table('books')->truncate();
        DB::connection(env('DB_CONNECTION'))->table('users')->truncate();

        \App\Models\User::factory(10)->create();
        \App\Models\Book::factory(10)->create();

        $users = User::all();
        $books = Book::all();
        $data = [];
        foreach($users as $user){
            $user_id = $user->id;
            foreach ($books as $book){
                $validate = random_int(0,1);
                if ($validate != 0){
                    $start_page = random_int(1,50);
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


        $chunks =array_chunk($data,1000);
        foreach ($chunks as $chunk){
            DB::connection(env('DB_CONNECTION'))->table('reading_intervals')->insert($chunk);
        }
    }


}
