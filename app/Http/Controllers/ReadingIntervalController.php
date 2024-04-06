<?php

namespace App\Http\Controllers;

use App\Events\IntervalSubmitted;
use App\Http\Requests\StoreReadingIntervalRequest;
use App\Http\Requests\UpdateReadingIntervalRequest;
use App\Http\Resources\BookResource;
use App\Http\Resources\ReadingIntervalResource;
use App\Models\Book;
use App\Models\ReadingInterval;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ReadingIntervalController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/intervals",
     *     operationId="IntervalsList",
     *     tags={"Intervals"},
     *     summary="Get all intervals",
     *     description="Retrieves all intervals of all inserted intervals by users",
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
     *         description="A list of intervals",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_name", type="string", example="Ahmed Osama"),
     *                 @OA\Property(property="book_name", type="string", example="Harry poter"),
     *                 @OA\Property(property="start_page", type="integer", example=1),
     *                 @OA\Property(property="end_page", type="integer", example=10),
     *                 @OA\Property(property="created_at", type="date")
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
        //
        $data = ReadingInterval::getPaginatedData($request->per_page,$request->page);

        return ReadingIntervalResource::collection($data);


    }


    /**
     * @OA\Post(
     *      path="/api/v1/intervals",
     *     operationId="storeInterval",
     *     tags={"Intervals"},
     *     summary="Add a new interval",
     *     description="Adds a new interval to the collection and returns the added interval details.
     *      [ Message will be sended after submit]",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Pass interval details",
     *         @OA\JsonContent(
     *             required={"user_id", "book_id","start_page","end_page"},
     *             @OA\Property(property="user_id", type="interger", example=10),
     *             @OA\Property(property="book_id", type="interger", example=1),
     *             @OA\Property(property="start_page", type="interger", example=1),
     *             @OA\Property(property="end_page", type="interger", example=15),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Interval successfully added",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="user_id", type="interger", example=10),
     *             @OA\Property(property="book_id", type="interger", example=1),
     *             @OA\Property(property="start_page", type="interger", example=1),
     *             @OA\Property(property="end_page", type="interger", example=15),
     *             @OA\Property(property="created_at", type="date")
     *         )
     *     ),
     *         @OA\Response(
     *         response=422,
     *         description="Error: Bad Request",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="You have entered this interval before"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="user_id",
     *                     type="array",
     *                     @OA\Items(type="string", example="You have entered this interval before"),
     *                 ),
     *             ),
     *         ),
     *     ),
     * )
     */
    public function store(StoreReadingIntervalRequest $request)
    {
        //
        $data = ReadingInterval::create(
            $request->only([
                'user_id',
                'book_id',
                'start_page',
                'end_page'
            ])
        );

        event(new IntervalSubmitted($data));

        return handleResponse($data,Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/intervals/{id}",
     *     operationId="getIntervalById",
     *     tags={"Intervals"},
     *     summary="Get a single Interval by ID",
     *     description="Retrieves the details of a single Interval given its ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the inteval to retrieve",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *              @OA\Property(property="id", type="integer", example=1),
     *              @OA\Property(property="user_id", type="string", example="1"),
     *              @OA\Property(property="book_id", type="string", example="1"),
     *              @OA\Property(property="start_page", type="integer", example=1),
     *              @OA\Property(property="end_page", type="integer", example=10),
     *              @OA\Property(property="created_at", type="date"),
     *              @OA\Property(property="updated_at", type="date")
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
        $data = ReadingInterval::findOrFail($id);
        return handleResponse($data,Response::HTTP_CREATED);

    }

    /**
     * @OA\Put(
     *     path="/api/v1/intervals/{id}",
     *     operationId="updateInterval",
     *     tags={"Intervals"},
     *     summary="Update a single Intervals's data",
     *     description="Updates the specified fields of a interval given its ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the interval to update",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Pass interval details",
     *         @OA\JsonContent(
     *             required={"user_id", "book_id","start_page","end_page"},
     *             @OA\Property(property="user_id", type="interger", example=10),
     *             @OA\Property(property="book_id", type="interger", example=1),
     *             @OA\Property(property="start_page", type="interger", example=1),
     *             @OA\Property(property="end_page", type="interger", example=15),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Interval successfully added",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="user_id", type="interger", example=10),
     *             @OA\Property(property="book_id", type="interger", example=1),
     *             @OA\Property(property="start_page", type="interger", example=1),
     *             @OA\Property(property="end_page", type="interger", example=15),
     *             @OA\Property(property="created_at", type="date")
     *         )
     *     ),
     *         @OA\Response(
     *         response=422,
     *         description="Error: Bad Request",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="You have entered this interval before"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="user_id",
     *                     type="array",
     *                     @OA\Items(type="string", example="You have entered this interval before"),
     *                 ),
     *             ),
     *         ),
     *     ),
     * )
     */
    public function update(UpdateReadingIntervalRequest $request, string $id)
    {

        $data = ReadingInterval::findOrFail($id);
        $data->update(
            $request->only([
                'user_id',
                'book_id',
                'start_page',
                'end_page'
            ])
        );
        return handleResponse($data,Response::HTTP_OK);

    }

    /**
     * @OA\Delete(
     *     path="/api/v1/intervals/{id}",
     *     operationId="deleteInterval",
     *     tags={"Intervals"},
     *     summary="Delete a single interval",
     *     description="Deletes an interval from the collection given its ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the interval to delete",
     *         @OA\Schema(
     *             type="integer",
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful Delete operation",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Book not found"
     *     )
     * )
     */
    public function destroy(string $id)
    {
        $interval = ReadingInterval::findOrFail($id);

        $interval->delete();

        return handleResponse([],Response::HTTP_OK);
    }
}
