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

    public function get($id)
    {
        $reactions = $this->returnToArray($this->reaction->where("post", "=", $id)->get());

        // get reactions names
        $raction_names = [];
        foreach ($reactions as $rnkey => $rnvalue) {
            array_push($raction_names, $rnvalue['reaction']);
        }
        $raction_names = array_unique($raction_names);

        // add numbers on reactions
        $react_numbres = [];
        foreach ($raction_names as $rmkey => $rmvalue) {
            $react_numbres[$rmvalue] = 0;
        }

        // add counts on reactions
        foreach ($reactions as $rnkey => $rnvalue) {
            $react_numbres[$rnvalue['reaction']]++;
        }

        return $react_numbres;
    }
    
    
}
