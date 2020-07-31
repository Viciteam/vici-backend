<?php

namespace App\Http\Services\Posts;

use App\Http\Services\BaseService;

use App\Data\Repositories\Posts\ReactionsRepository;

class ReactService extends BaseService
{   
    private $reactionsRepo;

    public function __construct(
        ReactionsRepository $reactionsRepo
    ){
        $this->react = $reactionsRepo;
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function handle(array $data)
    {   
        $post_id = $this->react->insert($data);
        
        if($post_id == ''){
            return $this->absorb([
                "status" => 500,
                "message" => "Something went wrong with inserting the reaction"
            ]);
        }

        return $this->absorb([
            "status" => 200,
            "message" => "Reaction Inserted"
        ]);
    }

}
