<?php
/**
 * Created by PhpStorm.
 * User: Buga1234
 * Date: 18.01.2017
 * Time: 23:25
 */

namespace App\Http\Controllers;


// declare  model using for users table

class WelcomeController extends Controller
{

    // all users table
    protected $users;

//welcome page index method
//shows view with users table
    public function index()
    {

        // getting all users from database
        $this->users = (new UserController())->getUsers();

        // show the welcome page
        return view('welcome', ['users' => $this->users]);
    }

}