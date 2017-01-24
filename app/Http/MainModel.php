<?php

namespace App\Http;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Auth;
use Carbon\Carbon;


class MainModel
{


    // user statis changing
    static function changeStatus($userId, $status)
    {

        /*
         *
         * update users where users.id == authenticated user id
         *
         * */

        DB::table('users')
            ->where("users.id", $userId)
            ->update(['users.status' => $status]);

    }


    // get user info by user id
    static function getUser($userId)
    {
        return DB::table('users')
            ->select('users.id', 'users.name', 'users.email', 'users.status', 'users.offline')
            ->where('users.id', $userId)->get()->first();
    }


    // get authenticated user info by user id
    static function getUserInfo($userId)
    {
        return DB::table('users')
            ->join('contact_list', 'users.id', '=', 'contact_list.users_id')
            ->select('users.id', 'users.name', 'users.email', 'users.status', 'users.offline', 'contact_list.id as contact_list_id')
            ->where('users.id', $userId)->get()->first();
    }

    // get users list for authenticated user if logged in
    // authenticated user will not be showed on list

    // if user is not logged in show all users list
    static function getUsers()
    {

        // check if user is authenticated
        if (Auth::check()) {

            return DB::table('users')
                ->select(DB::raw("users.id, users.name, users.email, users.status, users.offline,
                                (CASE WHEN EXISTS (SELECT chat.sender_id FROM chat
                        WHERE chat.sender_id = " . Auth::user()->id . " and chat.reciever_id = users.id)
                        THEN true ELSE false END) as inContactList"))
                ->where('users.id', '!=', Auth::user()->id)
                ->get();

        } else {

            return DB::table('users')
                ->select(DB::raw("users.id, users.name, users.email, users.status, users.offline"))
                ->get();


        }
    }

    // get user info for profile page
    static function getUserProfile($id)
    {
        // check if user is authenticated
        if (Auth::check()) {

            $user = DB::table('users')
                ->select(DB::raw("users.id, users.name, users.email, users.status, users.offline,
                                (CASE WHEN EXISTS (SELECT chat.sender_id FROM chat
                        WHERE chat.sender_id = " . Auth::user()->id . " and chat.reciever_id = users.id)
                        THEN true ELSE false END) as inContactList, contact_list.id as c_list_id"))
                ->join('contact_list', 'users.id', '=', 'contact_list.users_id')
                ->where('users.id', '=', $id)
                ->get()->first();

        } else {
            $user = DB::table('users')
                ->select(DB::raw("users.id, users.name, users.email, users.status, users.offline"))
                ->where('id', $id)->get()->first();

        }

        return $user;

    }

    // set users online status
    static function setUserOnlineStatus($offline)
    {

        DB::table('users')->where("users.id", Auth::user()->id)
            ->update(['users.offline' => $offline]);

    }


    // set unread messages as readed

    /*
     * $sender_id => UserController object for user who sends messages
     * $reciever_id => UserController object  for user who recieves messages
     *
     * */
    static function readMessages($sender_id, $reciever_id)
    {

        DB::table('message')
            ->where("message.reciever_id", $sender_id)
            ->where("message.sender_id", $reciever_id)
            ->where("message.unread", 1)
            ->update(['message.unread' => 0]);


    }


    // get messages for chat by $userId

    // recieved attribute is to find out which message was send by user chatting with
    // recieved == false if message was send by authenticated user
    static function getChatMessages($userId)
    {

        return DB::table('message')
            ->join('users as sender', 'message.sender_id', '=', 'sender.id')
            ->join('users as reciever', 'message.reciever_id', '=', 'reciever.id')
            ->select(DB::raw("message.*, sender.id,
                sender.name as sender_name,
                sender.email as sender_email,
                sender.status as sender_status,
                sender.offline as sender_offline,
                reciever.id as reciever_id,
                reciever.name as reciever_name,
                reciever.email as reciever_email,
                reciever.status as reciever_status, 
                reciever.offline as reciever_offline, 
                (CASE WHEN sender.id = " . Auth::user()->id . " THEN false ELSE true END) as recieved"))
            ->where('sender.id', Auth::user()->id)
            ->where('reciever.id', $userId)
            ->orWhere('sender.id', $userId)
            ->where('reciever.id', Auth::user()->id)
            ->orderBy('message.message_timestamp', 'asc')
            ->get();

    }


    // store messages
    static function setSentMessageToChat(Request $request, $chat)
    {

        return DB::table('message')->insert([
            'message_text' => $request->message,
            'message_timestamp' => Carbon::now(),
            'unread' => 1,
            'chat_id' => $chat->id,
            'sender_id' => $chat->sender_id,
            'reciever_id' => $chat->reciever_id,
            'contact_list_id' => $chat->contact_list_id
        ]);

    }

    // remove all messages from chat
    static function removeMessagesFromChat($chat)
    {

        DB::table('message')->where([
            ['chat_id', '=', $chat->id],
            ['contact_list_id', '=', $chat->contact_list_id],
            ['sender_id', '=', $chat->sender_id],
            ['reciever_id', '=', $chat->reciever_id]
        ])->delete();

    }


    // get contact list
    // unread_count => to find out which messages are unread and count them to show on page
    static function getContactList()
    {

        return DB::table('contact_list as c_list')
            ->join('chat', 'chat.sender_id', '=', 'c_list.users_id')
            ->join('users as sender', 'sender.id', '=', 'chat.sender_id')
            ->join('users as reciever', 'reciever.id', '=', 'chat.reciever_id')
            ->select(DB::raw("c_list.id as c_list_id, 
                              c_list.name as c_list_name,
                              sender.id as sender_id,
                              sender.name as sender_name,
                              sender.email as sender_email,
                              sender.status as sender_status,
                              sender.offline as sender_offline,
                              reciever.id as reciever_id,
                              reciever.name as reciever_name,
                              reciever.email as reciever_email,
                              reciever.status as reciever_status,
                              reciever.offline as reciever_offline,
                              (SELECT COUNT(*) FROM message WHERE 
                                                            message.reciever_id=chat.sender_id and 
                                                            message.sender_id=chat.reciever_id and 
                                                            message.unread=1) as unread_count"))
            ->where('c_list.users_id', Auth::user()->id)->get();

    }


    // remove chat
    static function removeChat($chatId)
    {
        DB::table('chat')->where('id', $chatId)->delete();
    }


    // get chat
    static function getChat($sender, $reciever)
    {

        $chat = DB::table('chat')
            ->where('sender_id', $sender->id)
            ->where('reciever_id', $reciever->id)
            ->get()->first();


        if (!$chat) {

            $chatId = DB::table('chat')->insertGetId([
                'sender_id' => $sender->id,
                'reciever_id' => $reciever->id
            ]);

            $chat = DB::table('chat')
                ->where('id', $chatId)
                ->get()->first();
        }

        return $chat;

    }


    // check chat existing
    static function checkChat($sender, $reciever)
    {
        return DB::table('chat')
            ->where('sender_id', $sender->id)
            ->where('reciever_id', $reciever->id)
            ->get()->first();
    }



    // add chat.

    // two chats for sender and reciever at the same time
    static function addChat($userToAdd, $contactListUser)
    {

        DB::table('chat')->insert([
            'reciever_id' => $userToAdd->id,
            'contact_list_id' => $contactListUser->contact_list_id,
            'sender_id' => $contactListUser->id
        ]);

        DB::table('chat')->insert([
            'reciever_id' => $contactListUser->id,
            'contact_list_id' => $userToAdd->contact_list_id,
            'sender_id' => $userToAdd->id
        ]);

    }

}