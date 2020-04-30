<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Validator;

class UserController extends Controller
{
   /*  public function __construct()
    {
        header('Access-Control-Allow-Origin: http://localhost:3000');
        
    } */
    //
    public function isAdmin($email)
    {
       // dd($email);
        //dd($request->all()['email']);
        $user = User::where('email',$email)->first();
 
        $role = !empty($user->userRole->role_name)? $user->userRole->role_name:null;
        if ($role == 'Administrator'){
            
            return response( ['error'=>false,
                                'data'=>true] , 200 )->
            header('Content-Type', 'application/json');
        }
        else{
            return response( ['error'=>false,
                                'data'=>false ] , 200 )->
            header('Content-Type', 'application/json');
        }
    }
}
