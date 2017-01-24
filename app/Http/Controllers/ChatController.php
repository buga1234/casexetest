<?php

namespace App\Http\Controllers;

use App\Http\MainModel;
use Illuminate\Support\Facades\DB;
use Auth;


// Controller for chat
class ChatController extends Controller
{


    // adding chat

    // $userToAdd => UserController object with user info

    // $userId => user id to add in chat

    // $contactListId => authenticated user contact list id

    // $contactListUserId => authenticated user id
    public function add($userToAdd, $contactListUser)
    {
        MainModel::addChat($userToAdd, $contactListUser);
    }


    // get chat

    // $sender => UserController object who will send messages
    // $reciever => UserController object to send messages
    public function getChat($sender, $reciever)
    {
        return MainModel::getChat($sender, $reciever);
    }

    // remove chat
    public function removeChat($chat)
    {
        MainModel::removeChat($chat->id);
    }
}