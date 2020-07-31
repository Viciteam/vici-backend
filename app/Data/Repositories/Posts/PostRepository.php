<?php


namespace App\Data\Repositories\Posts;

use App\Data\Models\Posts\PostModel;
use App\Data\Models\Posts\ReactionsModel;
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
class PostRepository extends BaseRepository
{
    /**
     * Declaration of Variables
     */
    private $post;
    private $reaction;
    private $meta;

    /**
     * PropertyRepository constructor.
     * @param Fund 
     */
    public function __construct(
        PostModel $postModel,
        ReactionsModel $reactModel,
        PostMetaModel $postMetaModel
    ){
        $this->post = $postModel;
        $this->reaction = $reactModel;
        $this->meta = $postMetaModel;
    }

    public function create($data)
    {
        $data['parent'] = (isset($data['parent']) ? $data['parent'] : "0");
        $data['level'] = (isset($data['level']) ? $data['level'] : "1");

        $post = $this->post->init($data);

        if (!$post->validate($data)) {
            $errors = $post->getErrors();
            return '';
        }

        //region Data insertion
        if (!$post->save()) {
            $errors = $post->getErrors();
            return '';
        }
        
        return $post->id;
    }

    public function getPosts($data)
    {   
        // get main posts
        if(isset($data['post_id'])){
            $posts = $this->returnToArray($this->post->where('id', '=', $data['post_id'])->orderBy('created_at', 'DESC')->get());
        } elseif(isset($data['position']) && isset($data['position_id'])){
            if($data['position'] == "user"){
                $posts = $this->returnToArray($this->post->where('user', '=', $data['position_id'])->orderBy('created_at', 'DESC')->get());
            } else {
                $posts = $this->returnToArray($this->post->where([['position', '=', $data['position']],['position_id', '=', $data['position_id']]])->orderBy('created_at', 'DESC')->get());
            }
            // $posts = $this->returnToArray($this->post->where('id', '=', $data['post_id'])->orderBy('created_at', 'DESC')->get());
        } else {
            $posts = $this->returnToArray($this->post->where([['parent', '=', '0'], ['ispublic', '=', 'public']])->orderBy('created_at', 'DESC')->get());
        }
        
        // get comments
        $posts_with_comments = [];
        foreach ($posts as $key => $value) {
            // get reactions for post
            $reactions = $this->returnToArray($this->reaction->where('post', '=', $value['id'])->orderBy('created_at', 'DESC')->get());

            // get second level
            $comment_section = [];
            $comments = $this->returnToArray($this->post->where('parent', '=', $value['id'])->orderBy('created_at', 'DESC')->get());
            
            // get third level
            if(!empty($comments)){
                foreach ($comments as $comkey => $comvalue) {
                    // get comment reaction
                    $comment_reactions = $this->returnToArray($this->reaction->where('post', '=', $comvalue['id'])->orderBy('created_at', 'DESC')->get());

                    // get sub-comments
                    $sub_comments = $this->returnToArray($this->post->where('parent', '=', $comvalue['id'])->orderBy('created_at', 'DESC')->get());
                    $comvalue['reaction'] = $comment_reactions;
                    $comvalue['replies'] = [];

                    // level 3 reply
                    foreach ($sub_comments as $ltkey => $ltvalue) {
                        $reply_reactions = $this->returnToArray($this->reaction->where('post', '=', $ltvalue['id'])->orderBy('created_at', 'DESC')->get());
                        $ltvalue['reaction'] = $reply_reactions;
                        array_push($comvalue['replies'], $ltvalue);
                    }
                    array_push($comment_section, $comvalue);
                }
            }

            $post_metas = $this->returnToArray($this->meta->where('post', '=', $value['id'])->orderBy('created_at', 'DESC')->get());
            
            //TODO: Change to update the Tags to user details (from users MS)
            // get items to values
            foreach ($post_metas as $mkey => $mvalue) {
                if($mvalue['meta_key'] == "share"){
                    $value['share'] = "";
                } else {
                    $value[$mvalue['meta_key']] = [];
                }
            }

            // get values for the meta
            foreach ($post_metas as $metakey => $metavalue) {
                if($metavalue['meta_key'] == "share"){
                    $sub_comments = $this->returnToArray($this->post->where('id', '=', $metavalue['meta_value'])->orderBy('created_at', 'DESC')->get());
                    if(!empty($sub_comments)){
                        $value['share'] = $sub_comments[0];
                    }
                } else {
                    array_push($value[$metavalue['meta_key']], $metavalue['meta_value']);
                }
            }


            $value['reaction'] = $reactions;
            $value['comments'] = $comment_section;
            
            array_push($posts_with_comments, $value);
        }
        
        return $posts_with_comments;
    }


    
    
}
