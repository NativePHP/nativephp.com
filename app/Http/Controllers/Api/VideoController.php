<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\YouTubeVideoService;
use Illuminate\Http\JsonResponse;

class VideoController extends Controller
{
    public function __invoke(YouTubeVideoService $videos): JsonResponse
    {
        return response()
            ->json(['data' => $videos->latest()])
            ->setPublic()
            ->setMaxAge(900);
    }
}
