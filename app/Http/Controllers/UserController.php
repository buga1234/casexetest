<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Http\MainModel;

class UserController extends Controller
{

    // change users status
    public function changeStatus(Request $request)
    {
        // change status
        MainModel::changeStatus($request->user_id, $request->status);

        // get back to the page
        return redirect()->back();
    }

    // get user
    public function getUser($userId)
    {
        return MainModel::getUser($userId);
    }


    // get user info with contact list id
    public function getUserInfo($userId)
    {
        return MainModel::getUserInfo($userId);
    }


    // get user info with contact list id
    // and set use as online
    public function getAuthUser()
    {
        // setting user status to online
        $this->setUserOnlineStatus(true);
        $result = MainModel::getUserInfo(Auth::user()->id);

        return $result;
    }


    // get users list for authenticated user
    public function getUsers()
    {
        return MainModel::getUsers();
    }

    // get user info by user id
    public function getUserProfile($id)
    {
        $user = MainModel::getUserProfile($id);

        // show view in user blade
        return view(
            'user',
            [
                'user' => $user,
            ]
        );
    }


    // setting user status to online
    public function setUserOnlineStatus($status)
    {
        // if $status == false -> $offline = 1 (user is offline)
        // if $status == true => $offline - 0 (user is online)
        $offline = $status ? 0 : 1;
        MainModel::setUserOnlineStatus($offline);
    }
}