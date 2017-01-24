<?php

namespace App\Http\Controllers;

use App\ContactList;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use App\Http\MainModel;
use Carbon\Carbon;

use Auth;


class MessageController extends Controller
{

    // users controller
    protected $users_ctrl;

    // users contact list controller
    protected $contact_list_ctrl;

    // chat controller
    protected $chat_ctrl;


    public function __construct()
    {

        // user controller init
        $this->users_ctrl = new UserController();

        // chat controller init
        $this->chat_ctrl = new ChatController();

    }


    // set messages as readed for user with $userId
    public function readMessages($userId)
    {

        // get user who sends messages
        $sender = $this->users_ctrl->getUser(Auth::user()->id);

        // get user to send messages
        $reciever = $this->users_ctrl->getUser($userId);


        // set messages as readed
        MainModel::readMessages($sender->id, $reciever->id);
    }


    // get all messages for active chat
    public function getChatMessages($userId)
    {
        // get all messages for active chat
        $result = MainModel::getChatMessages($userId);

        // if messages exist
        if ($result) {

            // set unread messages as readed
            $this->readMessages($userId);

        }

        return $result;
    }


    // send message to user from active chat
    public function sendMessage(Request $request)
    {

        // get user info who sends messages
        $sender_chatUser = MainModel::getUserInfo(Auth::user()->id);

        // get user info to send messages
        $reciever_chatUser = MainModel::getUserInfo($request->userId);

        // get chat to set messages
        $sender_chat = MainModel::checkChat($sender_chatUser, $reciever_chatUser);

        if ($sender_chat) {
            // set messages which was send to active users chat
            MainModel::setSentMessageToChat($request, $sender_chat);
        }

        // get back to page
        return redirect()->back()->withInput();
    }

    // remove messages from chat
    public function removeMessagesFromChat($chat)
    {
        // remove
        MainModel::removeMessagesFromChat($chat);
    }


}