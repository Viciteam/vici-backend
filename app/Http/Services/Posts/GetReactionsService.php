<?php

namespace App\Http\Services\Posts;

use App\Http\Services\BaseService;

use App\Data\Repositories\Posts\ReactionsRepository;

class GetReactionsService extends BaseService
{   
    private $reaction;

    public function __construct(
        ReactionsRepository $reactionRepo
    ){
        $this->reaction = $reactionRepo;
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function handle($id)
    {   
        $reactions = $this->reaction->get($id);

        return $this->absorb([
            "status" => 200,
            "message" => "Reactions fetched",
            "data" => $reactions
        ]);
    }

}
