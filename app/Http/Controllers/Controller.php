<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    /**
     * @OA\Info(
     *      version="1.0.0",
     *      title="Book Reccomendation",
     *      description="Get All Reccomendation Books",
     *      @OA\Contact(
     *          email="a.osamamahmoud96@gmail.com"
     *      ),
     * )
     */
    public function __construct()
    {
    }

}
