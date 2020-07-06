<?php

namespace App\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class SocialProfile extends Model{
    public function getProfile($data){

        $profile = DB::table('user_social_profile')
            ->where('user_id', $data)
            ->get();
        return $profile;
    }
    public function editProfile($data){

        $profile = DB::table('user_social_profile')
            ->where('user_id', $data->get('user_id'))
            ->update([
                'nickname' => $data->get('nickname'),
                'about_me' => $data->get('about_me'),
                'skills' => $data->get('skills'),
                'portfolio' => $data->get('portfolio'),
                'group' => $data->get('group'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        return $profile;
    }

    public function getConnections($data){

        $connections = DB::table('connection_map')
            ->select('users.id', 'users.name', 'users.email', 'status.status')
            ->leftJoin('users', 'connection_map.friend_id', '=', 'users.id')
            ->leftJoin('status', 'connection_map.status_id', '=', 'status.id')
            ->where('connection_map.user_id', $data)
            ->get();
        return $connections;
    }

    public function addConnection($data){
        $response = array();
        $message = "Successfully inserted user";
        $checker = DB::table('connection_map')
            ->select('id')
            ->where([
                ['user_id', '=', $data->get('user_id')],
                ['friend_id', '=', $data->get('friend_id')]
            ])
            ->exists();

        if ($checker){
            $message = "Connection request already sent.";
            $response = array(
                'msg' => $message,
                'status' => 'Failed'
            );
            return $response;
        }else{
            $add_connection = DB::table('connection_map')->insert([
                'user_id' => $data->get('user_id'),
                'friend_id' => $data->get('friend_id'),
                'status_id' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            $response = array(
                'msg' => $message,
                'status' => true
            );
            return $response;
        }

    }
}
