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
        $data['content'] = (isset($data['content']) ? $data['content'] : " ");

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

    public function time_elapsed_string($datetime, $full = false) {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);
    
        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;
    
        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }
    
        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }

    public function getPosts($data)
    {   
        // check if limit and page
        if(isset($data['limit']) || isset($data['page'])){
            if((!isset($data['limit']) || $data['limit'] == "") || (!isset($data['page']) || $data['page'] == "")){
                return [
                    'status' => 500,
                    'message' => 'Missing Limit or Page parameter',
                    'data' => [],
                ];
            }
        }

        // init model
        $post_model = $this->post;
        $pages = 0;
        $post_count = 0;

        // init skip values
        if(isset($data['limit']) && isset($data['page'])){ // get as per limit or page
            $skip = ($data['page'] == "1" ? 0 : ($data['page'] == "2" ? $data['limit'] : $data['limit'] * ($data['page'] - 1)));
        }

        // get main posts
        if(isset($data['post_id'])){ // get post as per id

            // init post
            $post_info = $post_model->where('id', '=', $data['post_id']);

            // get items with limit
            if(isset($data['limit']) && isset($data['page'])){ // get as per limit or page
                // get items for pagination
                $for_pagination = $post_info->get()->count();

                // get max number of pages
                $post_count = $for_pagination;
                $pages = ceil($for_pagination / $data['limit']);

                $post_info = $post_info->skip($skip)->take($data['limit']);
            }
            
        } elseif(isset($data['position']) && isset($data['position_id'])){ // get post as per position and position id
            
            // init post
            if($data['position'] == "user"){ // if position is user
                $post_info = $post_model->where([['parent', '=', '0'], ['user', '=', $data['position_id']]]);
            } else { // if other position is needed
                $post_info = $post_model->where([['parent', '=', '0'], ['position', '=', $data['position']],['position_id', '=', $data['position_id']]]);
            }

            if(isset($data['limit']) && isset($data['page'])){ // get as per limit or page
                // get items for pagination
                $for_pagination = $post_info->get()->count();

                // get max number of pages
                $post_count = $for_pagination;
                $pages = ceil($for_pagination / $data['limit']);

                $post_info = $post_info->skip($skip)->take($data['limit']);
            }
            
        } else {

            // init post
            $post_info = $post_model->where([['parent', '=', '0'], ['ispublic', '=', 'public']]);

            if(isset($data['limit']) && isset($data['page'])){ // get as per limit or page
                // get items for pagination
                $for_pagination = $post_info->get()->count();

                // get max number of pages
                $post_count = $for_pagination;
                $pages = ceil($for_pagination / $data['limit']);
                
                $post_info = $post_info->skip($skip)->take($data['limit']);
            }
        }

        // get post info
        $post_info = $post_info->orderBy('created_at', 'DESC')->get();
        $posts = $this->returnToArray($post_info);

        $posts_temp = [];
        foreach ($posts as $key => $value) {
            $value["posted_on"] = $this->time_elapsed_string($value['created_at']);
            array_push($posts_temp, $value);
        }
        $posts = $posts_temp;

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
                    $comvalue['posted_on'] = $this->time_elapsed_string($comvalue['created_at']);
                    // $comvalue['reaction'] = $comment_reactions;
                    $comvalue['reaction']['like'] = 0;
                    $comvalue['reaction']['dislike'] = 0;

                    foreach ($comment_reactions as $cr_key => $cr_value) {
                        if($cr_value['reaction'] == "like"){
                            $comvalue['reaction']['like']++;
                        }
                        if($cr_value['reaction'] == "dislike"){
                            $comvalue['reaction']['dislike']++;
                        }
                    }

                    $comvalue['replies'] = [];
                    

                    // level 3 reply
                    foreach ($sub_comments as $ltkey => $ltvalue) {
                        $reply_reactions = $this->returnToArray($this->reaction->where('post', '=', $ltvalue['id'])->orderBy('created_at', 'DESC')->get());
                        // $ltvalue['reaction'] = $reply_reactions;
                        $ltvalue['posted_on'] = $this->time_elapsed_string($ltvalue['created_at']);
                        $ltvalue['reaction']['like'] = 0;
                        $ltvalue['reaction']['dislike'] = 0;

                        foreach ($comment_reactions as $scr_key => $scr_value) {
                            if($scr_value['reaction'] == "like"){
                                $ltvalue['reaction']['like']++;
                            }
                            if($scr_value['reaction'] == "dislike"){
                                $ltvalue['reaction']['dislike']++;
                            }
                        }
                        
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


            // $value['reaction'] = $reactions;

            $value['reaction']['like'] = 0;
            $value['reaction']['dislike'] = 0;

            foreach ($reactions as $mcr_key => $mcr_value) {
                if($mcr_value['reaction'] == "like"){
                    $value['reaction']['like']++;
                }
                if($mcr_value['reaction'] == "dislike"){
                    $value['reaction']['dislike']++;
                }
            }
            $value['comments'] = $comment_section;
            
            array_push($posts_with_comments, $value);
        }
        
        return [
            "status" => 200,
            "message" => "Posts fetched",
            'meta' => [
                'max_pages' => $pages,
                'post_count' => $post_count
            ],
            "data" => $posts_with_comments
        ];
    }

    public function update($data)
    {
        $prods = $this->post->find($data['post_id']);

        if (!$prods) {
            return [
                "status" => 400,
                "message" => "Post not found",
                "data" => []
            ];
        }

        if (isset($data['post_id'])) {
            unset($data['post_id']);
        }

        $prods->fill($data);

        if (!$prods->save()) {
            $errors = $prods->getErrors();
            return [
                "status" => 500,
                "message" => "Something went wrong",
                "data" => $errors
            ];
        }

        return [
            "status" => 200,
            "message" => "Post updated successfully",
            "data" => $data
        ];
    }
    
    public function delete($id)
    {
        $projects = $this->post->find($id);

        if (!$projects) {
            return [
                'status' => 400,
                'message' => 'Post Details not found',
                'data' => [],
            ];
        }
        //endregion Existence check

        //region Data deletion
        if (!$projects->delete()) {
            $errors = $projects->getErrors();
            return [
                'status' => 500,
                'message' => 'Something went wrong.',
                'data' => $errors,
            ];
        }

        $this->meta->where("post", "=", $id)->delete();

        return [
            'status' => 200,
            'message' => 'Successfully deleted the Post.',
            'data' => [],
        ];
    }
}
