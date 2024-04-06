<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Http\Resources\BookResource;
use App\Http\Resources\ReadingIntervalResource;
use App\Http\Resources\TopBooksResource;
use App\Models\Book;
use App\Models\ReadingInterval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class BookController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/books",
     *     operationId="BooksList",
     *     tags={"Books"},
     *     summary="Get all books",
     *     description="Retrieves a list of all books in the collection.",
     *     @OA\Parameter(
     *         name="is_active",
     *         in="query",
     *         description="Filter between deleted and active books [ 1 => All Active Books , 0 => All In Active Books , -1 => All Books ]",
     *         @OA\Schema(
     *             default="1",
     *             type="integer",
     *             enum={1, 0, -1},
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of the rows per the page [ pagination ]",
     *         @OA\Schema(
     *             default="10",
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="the desired page number [ pagination ]",
     *         @OA\Schema(
     *             default="1",
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A list of books",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="A Brief History of Time"),
     *                 @OA\Property(property="number_of_pages", type="integer", example=256),
     *                 @OA\Property(property="is_active", type="boolean"),
     *                 @OA\Property(property="created_at", type="date"),
     *                 @OA\Property(property="updated_at", type="date")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error."
     *     )
     * )
     */
    public function index(Request $request)
    {
        $data = Book::getPaginatedData($request->per_page,$request->page);

        return BookResource::collection($data);
    }

    /**
     * @OA\Post(
     *      path="/api/v1/books",
     *     operationId="storeBook",
     *     tags={"Books"},
     *     summary="Add a new book",
     *     description="Adds a new book to the collection and returns the added book details.",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Pass book details",
     *         @OA\JsonContent(
     *             required={"title", "number_of_pages"},
     *             @OA\Property(property="title", type="string", example="A Brief History of Time"),
     *             @OA\Property(property="number_of_pages", type="integer", example=256)
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Book successfully added",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="title", type="string", example="A Brief History of Time"),
     *             @OA\Property(property="number_of_pages", type="integer", example=256),
     *             @OA\Property(property="is_active", type="boolean"),
     *             @OA\Property(property="created_at", type="date"),
     *             @OA\Property(property="updated_at", type="date")
     *         )
     *     ),
     *        @OA\Response(
     *         response=422,
     *         description="Error: Bad Request",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="You have entered this title before"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="title",
     *                     type="array",
     *                     @OA\Items(type="string", example="You have entered this interval before"),
     *                 ),
     *             ),
     *         ),
     *     ),
     * )
     */
    public function store(StoreBookRequest $request)
    {

        $data = Book::create(
                $request->only([
                    'title',
                    'number_of_pages'
                ])
            );
        return handleResponse($data,Response::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/books/{id}",
     *     operationId="getBookById",
     *     tags={"Books"},
     *     summary="Get a single book by ID",
     *     description="Retrieves the details of a single book given its ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the book to retrieve",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="title", type="string", example="A Brief History of Time"),
     *             @OA\Property(property="number_of_pages", type="integer", example=256),
     *             @OA\Property(property="is_active", type="boolean"),
     *             @OA\Property(property="created_at", type="date"),
     *             @OA\Property(property="updated_at", type="date")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Book not found"
     *     )
     * )
     */
    public function show(string $id)
    {
        $data = Book::findOrFail($id);
        return handleResponse($data,Response::HTTP_OK);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/books/{id}",
     *     operationId="updateBook",
     *     tags={"Books"},
     *     summary="Update a single book's data",
     *     description="Updates the specified fields of a book given its ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the book to update",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Book data to update",
     *         @OA\JsonContent(
     *             required={"title", "number_of_pages"},
     *             @OA\Property(property="title", type="string", example="Updated Book Title"),
     *             @OA\Property(property="number_of_pages", type="integer", example=123)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Book updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="Updated Book Title"),
     *             @OA\Property(property="number_of_pages", type="integer", example=123)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Book not found"
     *     ),
     *        @OA\Response(
     *         response=422,
     *         description="Error: Bad Request",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The title has already been taken."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="title",
     *                     type="array",
     *                     @OA\Items(type="string", example="The title has already been taken."),
     *                 ),
     *             ),
     *         ),
     *     ),
     * )
     */
    public function update(UpdateBookRequest $request, string $id)
    {
        $data = Book::findOrFail($id);
        $data->update(
                $request->only([
                'title',
                'number_of_pages'
                ])
            );
        return handleResponse($data,Response::HTTP_OK);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/books/{id}",
     *     operationId="deleteBook",
     *     tags={"Books"},
     *     summary="Delete a single book",
     *     description="Deletes a book from the collection given its ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the book to delete",
     *         @OA\Schema(
     *             type="integer",
     *
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful Delete operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="title", type="string", example="A Brief History of Time"),
     *             @OA\Property(property="number_of_pages", type="integer", example=256),
     *             @OA\Property(property="is_active", type="boolean",example=0),
     *             @OA\Property(property="created_at", type="date"),
     *             @OA\Property(property="updated_at", type="date")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Book not found"
     *     )
     * )
     */
    public function destroy(string $id)
    {
        $data = Book::findOrFail($id);
        $data->update(['is_active'=>0]);
        return handleResponse($data,Response::HTTP_OK);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/books/restore/{id}",
     *     operationId="restoreBook",
     *     tags={"Books"},
     *     summary="Restore a single book",
     *     description="Restore a book from the collection given its ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the book to restore",
     *         @OA\Schema(
     *             type="integer",
     *
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful restore operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="title", type="string", example="A Brief History of Time"),
     *             @OA\Property(property="number_of_pages", type="integer", example=256),
     *             @OA\Property(property="is_active", type="boolean",example=1),
     *             @OA\Property(property="created_at", type="date"),
     *             @OA\Property(property="updated_at", type="date")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Book not found"
     *     )
     * )
     */
    public function restore(string $id)
    {
        $data = Book::findOrFail($id);
        $data->update(['is_active'=>1]);
        return handleResponse($data,Response::HTTP_OK);
    }


    /**
     * @OA\Get(
     *     path="/api/v1/books/recommendations/top-five",
     *     operationId="topFiveBooks",
     *     tags={"Books"},
     *     summary="Get Top Five Recommendation books [ V1 Query Based ]",
     *     description="Retrieves a list of Top Five Recommendation books",
     *     @OA\Response(
     *         response=200,
     *         description="A List of Top Five Recommendation books",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="book_id", type="integer", example=1),
     *                 @OA\Property(property="book_name", type="string", example="A Brief History of Time"),
     *                 @OA\Property(property="num_of_read_pages", type="integer", example=256)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error."
     *     )
     * )
     */
    public function topFiveBooks(Request $request){
        $data = collect(ReadingInterval::topFiveReadBooks());
        return TopBooksResource::collection($data);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/books/recommendations/top-five/v2",
     *     operationId="topFiveBooksV2",
     *     tags={"Books"},
     *     summary="Get Top Five Recommendation books [ V2 Code & loops Based ]",
     *     description="Retrieves a list of Top Five Recommendation books",
     *     @OA\Response(
     *         response=200,
     *         description="A List of Top Five Recommendation books",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="book_id", type="integer", example=1),
     *                 @OA\Property(property="book_name", type="string", example="A Brief History of Time"),
     *                 @OA\Property(property="num_of_read_pages", type="integer", example=256)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error."
     *     )
     * )
     */
    public function topFiveBooksV2(Request $request){

        $listOfBooks = [];
        /**
         * By Sorting the data by [ book_id , start_page ] ASC
         * we are making sure the interval with the smallest start page number
         * and we will compare it with other intervals to get the unique pages only,
         * and we will initiate the first interval to compare with zeros
         * and based on the comparison we will either change the range of unique reading pages
         * or leave the range as it is , each part of code has its own explanation
         * */
        $results = ReadingInterval::select('reading_intervals.*','books.id as books_id','books.title as name')
            ->join('books','reading_intervals.book_id','=','books.id')
            ->orderBy('book_id','ASC')
            ->orderBy('start_page','ASC')
            ->get();

        foreach ($results as $result){
            if(!isset($listOfBooks[$result->book_id])) {
                $listOfBooks[$result->book_id] = [
                    'start_page' => 0,
                    'end_page' => 0,
                    'total_pages' => 0,
                    'book_name'=>$result->name,
                    'book_id'=>$result->book_id
                ];
            }
            $currentEndPage = $listOfBooks[$result->book_id]['end_page'];
            $currentTotalPages = $listOfBooks[$result->book_id]['total_pages'];
            if ($result->start_page > $currentEndPage && $result->end_page > $currentEndPage){
                /**
                 * Start page is greater than the  current end page
                 * and end page is greater than the current end page
                 * which means this interval is outside the last range
                 * so we will need to encounter add 1 to end page
                 * to make 40-50 = 11 not 10 as the user in that case has read 11 page not only 10
                 * as page number 40 and page number 50 is considered in the equation
                 */
                $currentTotalPages += ( $result->end_page + 1 ) - ( $result->start_page );
                $listOfBooks[$result->book_id]['start_page'] = $result->start_page;
                $listOfBooks[$result->book_id]['end_page'] =   $result->end_page;
                $listOfBooks[$result->book_id]['total_pages'] = $currentTotalPages ;
            }
            else if (($result->start_page < $currentEndPage && $result->end_page > $currentEndPage) || $result->start_page == $currentEndPage ){
                /**
                 * Start page is Less than the  current end page
                 * and end page is greater than the current end page
                 * which means this interval is inside the last range
                 * so no need to add 1 to end page
                 * OR
                 * the start page == to the current end page which means
                 * it is already calculated so no need to add the + 1 to end page
                 */
                $currentTotalPages += ($result->end_page ) - ($currentEndPage );
                $listOfBooks[$result->book_id]['start_page'] = $result->start_page;
                $listOfBooks[$result->book_id]['end_page']   = $result->end_page;
                $listOfBooks[$result->book_id]['total_pages'] = $currentTotalPages ;
            }
        }

        /**
         * using built in function provide by php to  sore the array by total_pages DESC
         * */
        array_multisort(array_column($listOfBooks, 'total_pages'),SORT_DESC,array_column($listOfBooks, 'book_id') , SORT_ASC, $listOfBooks);

        $listOfTopFiveBooks = collect([]);
        /**
         * Make sure there are already 5 books and catch them or all available books
        */
        count($listOfBooks) > 5 ?$counter = 5 :  $counter = count($listOfBooks);
        /** Get only first five */
        for ($i = 0 ; $i<$counter;$i++){
            $listOfTopFiveBooks[] = collect([
                'book_id'   => $listOfBooks[$i]['book_id'],
                'book_name' => $listOfBooks[$i]['book_name'],
                'num_of_read_pages' => $listOfBooks[$i]['total_pages']
            ]);
        }

        return handleResponse($listOfTopFiveBooks, Response::HTTP_OK);

    }

}
