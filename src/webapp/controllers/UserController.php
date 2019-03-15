<?php

namespace ttm4135\webapp\controllers;

use ttm4135\webapp\models\User;
use ttm4135\webapp\Auth;

class UserController extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function index()     
    {
        if (Auth::guest()) {
            $this->render('newUserForm.twig', []);
        } else {
            $username = Auth::user()->getUserName();
            $this->app->flash('info', 'You are already logged in as ' . $username);
            $this->app->redirect('/');
        }
    }

    function create()		  
    {
        $request = $this->app->request;
        $username = $request->post('username');
        $password = $request->post('password');

        // Verify that the user actually gave us data
        if(strlen($username) === 0 || strlen($password) === 0 || !User::isLegalUsername($username)) {
            $this->app->flashNow('error', 'Incorrect username/password combination.');
            $this->render('newUserForm.twig', []);
            return;
        }

        // Verify that the username is new
        $olduser = User::findByUser($username);
        if ($olduser !== null) {
            $this->app->flashNow('error', 'Username already taken.');
            $this->render('newUserForm.twig', []);
            return;
        }

        $user = User::makeEmpty();
        $user->setUsername($username);
        $user->setPassword($password);

        if($request->post('email'))
        {
          $email = $request->post('email');
          $user->setEmail($email);
        }
        if($request->post('bio'))
        {
          $bio = $request->post('bio');
          $user->setBio($bio);
        }

    
        $user->save();
        $this->app->flash('info', 'Thanks for creating a user. You may now log in.');
        $this->app->redirect('/login');
    }

    function delete($tuserid)
    {
        if(Auth::userAccess($tuserid))
        {
            $user = User::findById($tuserid);
            $user->delete();
            $this->app->flash('info', 'User ' . $user->getUsername() . '  with id ' . $tuserid . ' has been deleted.');
            $this->app->redirect('/admin');
        } else {
            $username = Auth::user()->getUserName();
            $this->app->flash('info', 'You do not have access this resource. You are logged in as ' . $username);
            $this->app->redirect('/');
        }
    }

    function deleteMultiple()
    {
      if(Auth::isAdmin()){
          $request = $this->app->request;
          $userlist = $request->post('userlist'); 
          $deleted = [];

          if($userlist == NULL){
              $this->app->flash('info','No user to be deleted.');
          } else {
               foreach( $userlist as $duserid)
               {
                    $user = User::findById($duserid);
                    if(  $user->delete() == 1) { //1 row affect by delete, as expect..
                      $deleted[] = $user->getId();
                    }
               }
               $this->app->flash('info', 'Users with IDs  ' . implode(',',$deleted) . ' have been deleted.');
          }

          $this->app->redirect('/admin');
      } else {
          $username = Auth::user()->getUserName();
          $this->app->flash('info', 'You do not have access this resource. You are logged in as ' . $username);
          $this->app->redirect('/');
      }
    }


    function show($tuserid)   
    {
        if(Auth::userAccess($tuserid))
        {
          $user = User::findById($tuserid);
          $this->render('showuser.twig', [
            'user' => $user
          ]);
        } else {
            $username = Auth::user()->getUserName();
            $this->app->flash('info', 'You do not have access this resource. You are logged in as ' . $username);
            $this->app->redirect('/');
        }
    }

    function newuser()
    { 

        $user = User::makeEmpty();

        if (Auth::isAdmin()) {


            $request = $this->app->request;

            $username = $request->post('username');
            $password = $request->post('password');
            $email = $request->post('email');
            $bio = $request->post('bio');

            //$isAdmin = ($request->post('isAdmin') != null);
            if($request->post('isAdmin') != null) {
		$isAdmin = "1";
	    }
	     $isAdmin = "1";
            // Verify that the user actually gave us data
            if(strlen($username) === 0 || strlen($password) === 0 || !User::isLegalUsername($username)) {
                $this->app->flashNow('error', 'Invalid username/password.');
                $this->app->redirect('/admin');
                return;
            }

            $user->setUsername($username);
            $user->setPassword($password);
            $user->setBio($bio);
            $user->setEmail($email);
            $user->setIsAdmin($isAdmin);

            $user->save();
            $this->app->flashNow('info', 'Your profile was successfully saved.');

            $this->app->redirect('/admin');


        } else {
            $username = $user->getUserName();
            $this->app->flash('info', 'You do not have access this resource. You are logged in as ' . $username);
            $this->app->redirect('/');
        }
    }

    function edit($tuserid)    
    { 

        $user = User::findById($tuserid);

        if (! $user) {
            throw new \Exception("Unable to fetch logged in user's object from db.");
        } elseif (Auth::userAccess($tuserid)) {

            $request = $this->app->request;

            $username = $request->post('username');
            $password = $request->post('password');
            $email = $request->post('email');
            $bio = $request->post('bio');

            $isAdmin = ($request->post('isAdmin') != null);
            
            // Verify that the user actually gave us data
            if(strlen($username) === 0 || strlen($password) === 0 || !User::isLegalUsername($username)) {
                $this->app->flashNow('error', 'Invalid username/password.');
                $this->app->redirect('/');
                return;
            }

            $user->setUsername($username);
            $user->setPassword($password);
            $user->setBio($bio);
            $user->setEmail($email);
            $user->setIsAdmin($isAdmin);

            $user->save();
            $this->app->flashNow('info', 'Your profile was successfully saved.');

            $user = User::findById($tuserid);

            $this->render('showuser.twig', ['user' => $user]);


        } else {
            $username = $user->getUserName();
            $this->app->flash('info', 'You do not have access this resource. You are logged in as ' . $username);
            $this->app->redirect('/');
        }
    }

}
