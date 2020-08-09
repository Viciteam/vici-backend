<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\SocialProfile;
use Throwable;

class SocialProfileController extends Controller
{
    public function getProfile(Request $request){
        $social_profile = new SocialProfile();
        $message = 'Success';
        //Check token here before pushing request
        //If there is a problem update message response:
        //$message ="Blah blah blah";
        $data = array();

        try {
            $data = $social_profile->getProfile($request->json("user_id"));
            $message = $data['status'];
        } catch (Throwable $e) {
            $message = $e;
        }

        $response = array(
            'status' => http_response_code(),
            "data" => $data['data'],
            'message' => $message
        );

        return $response;
    }
    public function editProfile(Request $request){
        $social_profile = new SocialProfile();
        $message = 'Success';
        //Check token here before pushing request
        //If there is a problem update message response:
        //$message ="Blah blah blah";
        $data = array();

        try {
            $data = $social_profile->editProfile($request->json());
        } catch (Throwable $e) {
            $message = $e;
        }

        $response = array(
            'status' => http_response_code(),
            "data" => $data,
            'message' => $message
        );

        return $response;
    }
    public function addConnection(Request $request){
        $social_profile = new SocialProfile();
        //Check token here before pushing request
        //If there is a problem update message response:
        //$message ="Blah blah blah";
        $data = array();

        try {
            $data = $social_profile->addConnection($request->json());
            $message = $data['msg'];
        } catch (Throwable $e) {
            $message = $e;
        }

        $response = array(
            'status' => http_response_code(),
            "data" => $data['status'],
            'message' => $message
        );

        return $response;
    }
    public function getConnections(Request $request){
        $social_profile = new SocialProfile();
        $message = 'Success';
        //Check token here before pushing request
        //If there is a problem update message response:
        //$message ="Blah blah blah";
        $data = array();
        try {
            $data = $social_profile->getConnections($request->json("user_id"));
        } catch (Throwable $e) {
            $message = $e;
        }

        $response = array(
            'status' => http_response_code(),
            "data" => $data,
            'message' => $message
        );

        return $response;
    }
    public function acceptConnectionRequest(Request $request){
        $social_profile = new SocialProfile();
        //Check token here before pushing request
        //If there is a problem update message response:
        //$message ="Blah blah blah";
        $data = array();

        try {
            $data = $social_profile->acceptConnectionRequest($request->json());
            $message = $data['msg'];
        } catch (Throwable $e) {
            $message = $e;
        }

        $response = array(
            'status' => http_response_code(),
            "data" => $data['status'],
            'message' => $message
        );

        return $response;
    }
    public function getSuggestedConnections(Request $request){
        $social_profile = new SocialProfile();
        $message = 'Success';
        //Check token here before pushing request
        //If there is a problem update message response:
        //$message ="Blah blah blah";
        $data = array();

        try {
            $data = $social_profile->getSuggestedConnections($request->json("user_id"));
            $message = $data['status'];
        } catch (Throwable $e) {
            $message = $e;
        }

        $response = array(
            'status' => http_response_code(),
            "data" => $data['data'],
            'message' => $message
        );

        return $response;
    }
    public function followConnection(Request $request){
        $social_profile = new SocialProfile();
        //Check token here before pushing request
        //If there is a problem update message response:
        //$message ="Blah blah blah";
        $data = array();

        try {
            $data = $social_profile->followConnection($request->json());
            $message = $data['msg'];
        } catch (Throwable $e) {
            $message = $e;
        }

        $response = array(
            'status' => http_response_code(),
            "data" => $data['status'],
            'message' => $message
        );

        return $response;
    }
    public function unFollowConnection(Request $request){
        $social_profile = new SocialProfile();
        //Check token here before pushing request
        //If there is a problem update message response:
        //$message ="Blah blah blah";
        $data = array();

        try {
            $data = $social_profile->unFollowConnection($request->json());
            $message = $data['msg'];
        } catch (Throwable $e) {
            $message = $e;
        }

        $response = array(
            'status' => http_response_code(),
            "data" => $data['status'],
            'message' => $message
        );

        return $response;
    }
}
