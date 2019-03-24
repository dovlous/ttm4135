<?php


namespace ttm4135\webapp\controllers;
use ttm4135\webapp\Auth;
use ttm4135\webapp\models\User;




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

        if(isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response']))
        {
            $secret = '6LeMH5kUAAAAAFEUUMvTA9ZeUoQ_3dxDI6P8hIGM';
            $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$_POST['g-recaptcha-response']);
            $responseData = json_decode($verifyResponse);
            if($responseData->success)
            {
                $succMsg = 'Your contact request have submitted successfully.';
                echo "naaasis";
            }
            else
            {
                $errMsg = 'Robot verification failed, please try again.';
                echo "faiiil";
            }
        }


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
