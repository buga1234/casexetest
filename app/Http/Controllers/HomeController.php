<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;

class HomeController extends Controller
{

    // authenticated user
    protected $user;

    // users list
    protected $users;

    // users contact list
    protected $contactList;

    // active user id in chat
    protected $activeUserId;

    // chat with messages
    protected $chat;

    // users controller
    protected $users_ctrl;

    // users contact list controller
    protected $contact_list_ctrl;


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // check if user is logged in. Redirect to login page if not
        $this->middleware('auth');

        //users controller init
        $this->users_ctrl = new UserController();

        // contact list controller init
        $this->contact_list_ctrl = new ContactListController();

    }

    /**
     * Show the application home page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // getting authenticated user info
        $this->user = $this->users_ctrl->getAuthUser();

        // getting all users from database
        $this->users = $this->users_ctrl->getUsers();

        // get contact list
        $this->contactList = $this->contact_list_ctrl->getContactList();

        // check route state action to get user chattiing with
        $this->checkChatAction($request);

        // show view in home blade
        return view(
            'home',
            [
                'users' => $this->users,
                'user' => $this->user,
                'contactList' => $this->contactList,
                'activeUserId' => $this->activeUserId,
                'chat' => $this->chat
            ]
        );
    }


    // check route state action
    public function checkChatAction($request)
    {

        // message controller init
        $message = new MessageController();


        // if route state have a in query string action=getChat => get chat by chatId in query string
        if ($request->action === 'getChat') {

            // set active user for chat
            $this->activeUserId = $request->userId;

            $this->chat = $message->getChatMessages($request->userId);

            // else get first chat from contact list if exists
        } else {

            // if contact list has users
            if (count($this->contactList) > 0) {

                // set first active user for chat from contact list
                $this->activeUserId = $this->contactList[0]->reciever_id;

                // get chat and its messages
                $this->chat = $message->getChatMessages($this->contactList[0]->reciever_id);
            }

        }
    }
}
