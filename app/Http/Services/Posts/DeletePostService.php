<?php

namespace App\Http\Services\Posts;

use App\Http\Services\BaseService;

use App\Data\Repositories\Posts\PostRepository;
use App\Data\Repositories\Posts\PostMetaRepository;

class DeletePostService extends BaseService
{   
    private $postRepo;
    private $postMetaRepo;
    private $reactionsRepo;

    public function __construct(
        PostRepository $postRepo,
        PostMetaRepository $postMetaRepo
    ){
        $this->post = $postRepo;
        $this->meta = $postMetaRepo;
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function handle($data)
    {   
        $posts = $this->post->delete($data);

        return $this->absorb($posts);
    }

}
