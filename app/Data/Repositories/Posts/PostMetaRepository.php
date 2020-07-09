<?php


namespace App\Data\Repositories\Posts;

use App\Data\Models\Posts\PostMetaModel;

use App\Data\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use DateTime;

/**
 * Class FundRepository
 *
 * @package App\Data\Repositories\Users
 */
class PostMetaRepository extends BaseRepository
{
    /**
     * Declaration of Variables
     */
    private $post_meta;

    /**
     * PropertyRepository constructor.
     * @param Fund 
     */
    public function __construct(
        PostMetaModel $postMetaModel
    ){
        $this->post_meta = $postMetaModel;
    }

    public function insert($id, $data)
    {
        foreach ($data as $key => $value) {
            // as per meta key
            foreach ($value as $innerkey => $innervalue) {
                // meta values
                $meta = [
                    "post" => $id,
                    "meta_key" => $key,
                    "meta_value" => $innervalue,
                ];

                $insert_meta = $this->post_meta->init($meta);
                
                if (!$insert_meta->save()) {
                    $errors = $insert_meta->getErrors();
                    continue;
                }
            }
        }
    }


    
    
}
