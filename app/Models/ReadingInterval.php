<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ReadingInterval extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'book_id',
        'start_page',
        'end_page'
    ];


    public function user(){
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function book(){
        return $this->belongsTo(Book::class,'book_id','id');

    }


    public static function getPaginatedData($perPage = 10, $page = 1){
        return
            ReadingInterval::select(
            'reading_intervals.id',
            'users.name as user_name',
            'books.title as book_name',
            'reading_intervals.start_page as start_page',
            'reading_intervals.end_page as end_page',
            'reading_intervals.created_at as created_at')
            ->join('users','reading_intervals.user_id','users.id')
            ->join('books','reading_intervals.book_id','books.id')
            ->orderBy('reading_intervals.id','DESC')
            ->paginate($perPage, ['*'], 'page', $page);
    }

    public static function topFiveReadBooks(){
        return DB::select('
        WITH expanded_intervals AS (
        SELECT
            book_id,
            generate_series(start_page, end_page) AS page
        FROM
            reading_intervals
        ),
        unique_page_counts AS (
            SELECT
                book_id,
                COUNT(DISTINCT page) AS unique_pages_read
            FROM
                expanded_intervals
            GROUP BY
                book_id
        )
        SELECT
            b.id as book_id,
            b.title as book_name ,
            u.unique_pages_read as num_of_read_pages
        FROM
            unique_page_counts u
        JOIN
            books b ON u.book_id = b.id
        ORDER BY
            unique_pages_read DESC , book_id ASC
        LIMIT 5;');
    }


    /**
     * a trial to do the same logic with mySql but CTEs give an awful performance
     *
       public static function topFiveReadingBooks(){
            return DB::select("
                            WITH RECURSIVE possible_pages AS (
                                SELECT 1 AS n
                                UNION ALL
                                SELECT n + 1 FROM possible_pages WHERE n < 10000
                            ),
                            all_read_pages AS (
                                SELECT reading_intervals.book_id,
                                       possible_pages.n AS page_number
                                FROM reading_intervals
                                JOIN possible_pages ON possible_pages.n BETWEEN reading_intervals.start_page AND reading_intervals.end_page
                            )
                           SELECT  all_read_pages.book_id as book_id,
                                    books.title as book_name ,
                                    COUNT(DISTINCT page_number) as num_of_read_pages
                            FROM all_read_pages
                            JOIN books ON all_read_pages.book_id = books.id
                            GROUP BY book_id
                            ORDER BY num_of_read_pages DESC
                            LIMIT 5;");
        }
     *
     *
     */

}
