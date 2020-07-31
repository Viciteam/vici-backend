<?php


namespace App\Data\Repositories\Posts;

use App\Data\Models\Posts\ReactionsModel;

use App\Data\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use DateTime;

/**
 * Class FundRepository
 *
 * @package App\Data\Repositories\Users
 */
class ReactionsRepository extends BaseRepository
{
    /**
     * Declaration of Variables
     */
    private $reaction;

    /**
     * PropertyRepository constructor.
     * @param Fund 
     */
    public function __construct(
        ReactionsModel $reactionModel
    ){
        $this->reaction = $reactionModel;
    }

    public function insert($data)
    {
        $reaction = $this->reaction->init($data);

        if (!$reaction->validate($data)) {
            $errors = $reaction->getErrors();
            return '';
        }

        //region Data insertion
        if (!$reaction->save()) {
            $errors = $reaction->getErrors();
            return '';
        }
        
        return $reaction->id;
    }
    
    
}
