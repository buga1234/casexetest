<?php

namespace App\Http\Controllers;

use App\Http\MainModel;
use Illuminate\Support\Facades\DB;
use Auth;

class ContactListController extends Controller
{

    // user controller
    protected $users_ctrl;

    // chat
    protected $chat;

    // message controller
    protected $messages_ctrl;


    // contact list construct
    public function __construct()
    {

        // UserController init
        $this->users_ctrl = new UserController();

        // ChatController inir
        $this->chat_ctrl = new ChatController();

        // MessageController init
        $this->messages_ctrl = new MessageController();

    }


    // adding user to contact list
    public function addToContactList($userId, $contactListUserId)
    {

        // get user info for adding to contact list
        $userToAdd = $this->users_ctrl->getUserInfo($userId);

        // get user info - contact list owner
        $contactListUser = $this->users_ctrl->getUserInfo($contactListUserId);

        // add user to contact list

        // $userToAdd -> UserController object
        // $userId => id of user to add in contact list
        // $contactListId => authenticated user contact list id
        // $contactListUserId => authenticated user id
        $this->chat_ctrl->add($userToAdd, $contactListUser);

        return redirect()->back();
    }


    // removing user from contact list

    // $userId => removing user id
    // $contactListUserId => autheticated user id
    public function removeFromContactList($userId, $contactListUserId)
    {

        // get authenticated user info (is message sender)
        $sender = $this->users_ctrl->getUserInfo($contactListUserId);

        // get user info chatting with (is message reciever)
        $reciever = $this->users_ctrl->getUserInfo($userId);

        // get sender chat info
        $sender_chat = $this->chat_ctrl->getChat($sender, $reciever);

        // get reciever chat info
        $reciever_chat = $this->chat_ctrl->getChat($reciever, $sender);

        // remove all messages from sender chat
        $this->messages_ctrl->removeMessagesFromChat($sender_chat);

        // remove all messages from reciever chat
        $this->messages_ctrl->removeMessagesFromChat($reciever_chat);

        // remove sender chat
        $this->chat_ctrl->removeChat($sender_chat);

        // remove reciever chat
        $this->chat_ctrl->removeChat($reciever_chat);

        return redirect()->back();
    }


    // getting uthenticated users contact list
    public function getContactList()
    {
        return MainModel::getContactList();
    }


}