<?php

namespace App\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class SocialProfile extends Model{
    public function getProfile($data){
        $message ='';
        $res = array();
        $user_id_exists = DB::table('users')
            ->select('id')
            ->where('id', '=', $data)
            ->exists();
        if($user_id_exists){
            $profile = DB::table('user_social_profile')
                ->where('user_id', $data)
                ->get();
            $res = $profile;
            $message = true;
        }else{
            $res = '{No user found.}';
            $message = false;
        }


        $response = array(
            'data' => $res,
            'status' => $message
        );
        return $response;
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
        $status = false;
        $message = "Successfully inserted user";
        $user_id_exists = DB::table('users')
            ->select('id')
            ->where('id', '=', $data->get('user_id'))
            ->exists();
        if($user_id_exists){
            $friend_id_exists =  DB::table('users')
                ->select('id')
                ->where('id', '=', $data->get('friend_id'))
                ->exists();
            if($friend_id_exists){
                $checker = DB::table('connection_map')
                    ->select('id')
                    ->where([
                        ['user_id', '=', $data->get('user_id')],
                        ['friend_id', '=', $data->get('friend_id')]
                    ])
                    ->exists();

                if ($checker){
                    $message = "Connection request already sent.";
                    $status = false;
                }else{
                    $add_connection = DB::table('connection_map')->insert([
                        'user_id' => $data->get('user_id'),
                        'friend_id' => $data->get('friend_id'),
                        'status_id' => 1,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                    $add_connection = DB::table('connection_map')->insert([
                        'user_id' => $data->get('friend_id'),
                        'friend_id' => $data->get('user_id'),
                        'status_id' => 2,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                    $status = true;
                }
            }else{
                $message = "Friend ID can't be found.";
                $status = false;
            }
        }else{
            $message = "User ID can't be found.";
            $status = false;
        }

        $response = array(
            'msg' => $message,
            'status' => $status
        );
        return $response;

    }

    public function acceptConnectionRequest($data){

        $response = array();
        $status = false;
        $message = "Successfully updated user";
        $user_id_exists = DB::table('users')
            ->select('id')
            ->where('id', '=', $data->get('user_id'))
            ->exists();
        if($user_id_exists){
            $friend_id_exists =  DB::table('users')
                ->select('id')
                ->where('id', '=', $data->get('friend_id'))
                ->exists();

            if($friend_id_exists){
                $checker = DB::table('connection_map')
                    ->select('id')
                    ->where([
                        ['user_id', '=', $data->get('user_id')],
                        ['friend_id', '=', $data->get('friend_id')],
                        ['status_id', '=', 2]
                    ])
                    ->exists();

                if ($checker){
                    $accept = DB::table('connection_map')
                        ->where([
                            ['user_id', '=', $data->get('user_id')],
                            ['friend_id', '=', $data->get('friend_id')],
                            ['status_id', '=', 2]
                        ])
                        ->update(['status_id' => 3]);
                    $update_accept = DB::table('connection_map')
                        ->where([
                            ['user_id', '=', $data->get('friend_id')],
                            ['friend_id', '=', $data->get('user_id')],
                            ['status_id', '=', 1]
                        ])
                        ->update(['status_id' => 3]);
                    $status = true;
                }else{


                    $message = "Friend request not found.";
                    $status = false;

                }
            }else{
                $message = "Friend ID can't be found.";
                $status = false;
            }
        }else{
            $message = "User ID can't be found.";
            $status = false;
        }

        $response = array(
            'msg' => $message,
            'status' => $status
        );
        return $response;

    }

    public function getSuggestedConnections($data){
        $message ='';
        $res = array();

        $user_id_exists = DB::table('users')
            ->select('id')
            ->where('id', '=', $data)
            ->exists();

        if($user_id_exists){
            $suggested_connections = DB::table('users as u')
                ->join('connection_map as cm', 'u.id', '=', 'cm.user_id')
                ->join('connection_map as cm2', 'cm.friend_id', '=', 'cm2.user_id')
                ->join('users as uf', 'cm2.friend_id', '=', 'uf.id')
                ->select('uf.*', DB::raw('SUM(IF(uf.id = cm2.friend_id, 1, 0)) AS mutual'))
                ->where('u.id', '=', $data)
                ->where('uf.id', '!=', $data)
                ->where('cm2.status_id', '=', 3)
                ->whereRaw("NOT EXISTS(SELECT 1 FROM connection_map c WHERE c.user_id = ? AND uf.id = c.friend_id)",[$data])
                ->groupBy('uf.id')
                ->get();
            $res =  $suggested_connections;
            $message = true;
        }else{
            $res = '{No user found.}';
            $message = false;
        }


        $response = array(
            'data' => $res,
            'status' => $message
        );
        return $response;
    }

}
