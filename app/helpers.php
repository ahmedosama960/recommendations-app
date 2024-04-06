<?php

if (!function_exists('handleResponse')) {
    function handleResponse($data, $code, $meta = null)
    {
        return response()->json([
            "data" => $data,
            "meta" => $meta
        ], $code);
    }
}
