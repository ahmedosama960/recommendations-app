<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/users",
     *     operationId="usersList",
     *     tags={"User"},
     *     summary="Get all Users",
     *     description="Retrieves a list of all users in the collection.",
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
     *         description="A list of users",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Ahmed Osama"),
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
        $data = User::getPaginatedData($request->per_page, $request->page);

        return UserResource::collection($data);
    }
}
