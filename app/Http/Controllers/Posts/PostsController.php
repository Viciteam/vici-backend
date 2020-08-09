<?php

namespace App\Http\Controllers\Posts;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Services\Posts\InsertPostService;
use App\Http\Services\Posts\GetPostsService;
use App\Http\Services\Posts\ReactService;

class PostsController extends Controller
{
    public function create(
        Request $request,
        InsertPostService $insertPostService
    )
    {
        $data = $request->all();
        $post = $insertPostService->handle($data);
        return $post;
    }

    public function react(
        Request $request,
        ReactService $reactService
    )
    {
        $data = $request->all();
        $post = $reactService->handle($data);
        return $post;
    }

    public function posts(
        Request $request,
        GetPostsService $getPostsServices
    )
    {
        $data = $request->all();
        $post = $getPostsServices->handle($data);
        return $post;
    }

    public function single(
        Request $request,
        GetPostsService $getPostsServices,
        $id
    )
    {
        $data = $request->all();
        $data['post_id'] = $id;
        $post = $getPostsServices->handle($data);
        return $post;
    }
}
