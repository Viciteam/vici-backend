<?php

namespace App\Http\Services\Posts;

use App\Http\Services\BaseService;

use App\Data\Repositories\Posts\PostRepository;
use App\Data\Repositories\Posts\PostMetaRepository;

class InsertPostService extends BaseService
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
    public function handle(array $data)
    {   
        $post_id = $this->post->create($data);
        
        if($post_id == ''){
            return $this->absorb([
                "status" => 500,
                "message" => "Something went wrong with inserting the post"
            ]);
        }

        // insert meta
        $this->meta->insert($post_id, $data['meta']);

        return $this->absorb([
            "status" => 200,
            "message" => "Post Inserted"
        ]);
    }

}
