<?php

use App\Helpers\ApiResponse;

if (!function_exists('apiResponse')) {
    function apiResponse(): ApiResponse
    {
        return new ApiResponse();
    }
}
