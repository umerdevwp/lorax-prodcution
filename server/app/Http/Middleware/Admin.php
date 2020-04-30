<?php

namespace App\Http\Middleware;

use Closure;
use App\User;
use Exception;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $token = $request->header('authorization');
        $nonce = $request->header('nonce');
        $sub = $request->header('sub');
        $email = $request->header('email');
        if(User::where('token',$token)->where('expired_at','>',date('Y-m-d H:i:s'))->first()===null){
            $addResult = $this->addToken($sub,$email,$token,$nonce); 
            if($addResult !==true){
                return $addResult;
           }     
        }
        
        return $next($request);
    }
    
    public function addToken($sub, $email, $token, $nonce)
    {
        try{
            
                $jwtVerifier = (new \Okta\JwtVerifier\JwtVerifierBuilder())
                ->setDiscovery(new \Okta\JwtVerifier\Discovery\Oauth) // This is not needed if using oauth.  The other option is OIDC
                ->setAdaptor(new \Okta\JwtVerifier\Adaptors\FirebasePhpJwt)
                //->setAudience('api://default')
                ->setAudience('0oa2l9y2sMSBKcaoa4x6')
                ->setClientId('0oa2l9y2sMSBKcaoa4x6')
                ->setNonce($nonce)
                ->setIssuer('https://dev-143279.okta.com/oauth2/default')
                ->build();

                $jwt = $jwtVerifier->verify($token);
            
       //Returns instance of \Okta\JwtVerifier\JWT
          $expired_on = $jwt->getExpirationTime(false);
         // dd($expired_on);
          // add token in database
          if(!empty($expired_on)) {
            $checkUser = User::where('email',$email)->first();
            if($checkUser!==null){
                try{
                    $checkUser->token  = $token;
                    $checkUser->sub = $sub;
                    $checkUser->expired_at = date('Y-m-d H:i:s',$expired_on);
                    $checkUser->save();
                    return true;
                }
                catch( Exception $e){
                    return response([
                        'error'=>true,
                        'message'=>'User Not Authorized!'], 401)->
                    header('Content-Type', 'application/json'); 
                }
            }
            else{
                return response([
                    'error'=>true,
                    'message'=>'User Not Authorized!'], 401)->
                header('Content-Type', 'application/json');
            }
        }
        else{
            return response([
                'error'=>true,
                'message'=>'User Not Authorized!'], 401)->
            header('Content-Type', 'application/json');
        }

        }
        catch( Exception $e){
            return response([
                'error'=>true,
                'message'=>'User Not Authorized!'], 401)->
            header('Content-Type', 'application/json'); 
        }

    }

    public function hasToken($token)
    {

        return User::where('token',$token)->first();

    }
    public function deletePreviousToken($sub){
        //$CI =& get_instance();
        //$CI->load->model("Auth_model");
        //return $CI->Auth_model->delete($sub);
    }

    
}
