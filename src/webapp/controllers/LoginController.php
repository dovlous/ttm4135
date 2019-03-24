<?php

namespace ttm4135\webapp\controllers;
use ttm4135\webapp\Auth;
use ttm4135\webapp\models\User;


// your secret key
$secret = "6LcePAATAAAAABjXaTsy7gwcbnbaF5XgJKwjSNwT";

// empty response
$response = null;

// check secret key
$reCaptcha = new ReCaptcha($secret);

class LoginController extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function index()
    {
        if (Auth::check()) {
            $username = Auth::user()->getUserName();
            $this->app->flash('info', 'You are already logged in as ' . $username);
            $this->app->redirect('/');
        } else {
            $this->render('login.twig', ['title'=>"Login"]);
        }
    }

    function login()
    {
        $request = $this->app->request;
        $username = $request->post('username');
        $password = $request->post('password');

        if ( strlen($username) === 0 && strlen($password) === 0 ) {
            $this->app->flashNow('error', 'Incorrect username/password combination.');
            $this->render('login.twig', []);
            return;
        }

        if ( Auth::checkCredentials($username, $password) ) {
            session_regenerate_id();
            $user = User::findByUser($username);
            $_SESSION['userid'] = $user->getId();
            $this->app->flash('info', "You are now successfully logged in as " . $user->getUsername() . ".");
            $this->app->redirect('/');
        } else {
            $this->app->flashNow('error', 'Incorrect username/password combination.');
            $this->render('login.twig', []);
        }
    }

    function logout()
    {   
        Auth::logout();
        $this->app->flashNow('info', 'Logged out successfully!!');
        $this->render('base.twig', []);
        return;
       
    }
}
