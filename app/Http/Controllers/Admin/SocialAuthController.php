<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Cookie;

class SocialAuthController extends CommonController
{
    public function redirect($provider)
    {
        //dd($provider);

        return Socialite::driver($provider)->redirect();
    }

    public function callback($provider)
    {
        //dd(Socialite::driver('facebook')->user());
        //$facebook_data=Socialite::driver('facebook')->user();

        //$Socialite_data=Socialite::driver($provider)->user();
        $Socialite_data = Socialite::driver( $provider )->stateless()->user();
        //dd($Socialite_data);


        $provider_user_id=$Socialite_data->getId();
        $email=$Socialite_data->getEmail();
        $name=$Socialite_data->getName();
        //dd($provider_user_id, $email, $name);

        $email_check=User::where('provider_user_id',"!=",$provider_user_id)
            ->where('email',$email)
            ->get();
        //$email_check = User::where('provider_user_id', $provider_user_id)->get();

        if($email_check->count()>0){
            $error_provider=$email_check[0]->provider;
            return redirect('/login')->with('msg','此帳號已用'.$error_provider.'帳號註冊會員!');
        }

        $user_account=User::WHERE('provider_user_id',$provider_user_id)->get();
        // dd($user_account);
        if($user_account->count()>0){
            $user = $user_account->first();
            $data = [
                'email'=> $email,
                'password'=> '12345678',
            ];
            //dd(json_encode($data));
            $uri = env('APP_URL').'/users/login';
            $client = new \GuzzleHttp\Client(['verify' => false]);
            //$res = $client->request('POST', 'http://localhost:8080/users/login',['form_params' => $input] );
            //$request = new \GuzzleHttp\Psr7\Request('POST', 'http://appserver.yesio.net:8080/users/login', ['Content-type' => 'application/json'], json_encode($input) );
            $request = new \GuzzleHttp\Psr7\Request('POST', $uri, ['Content-type' => 'application/json'], json_encode($data) );
            $token = '';
            $promise = $client->sendAsync($request)->then(function ($response) {
                $data = json_decode($response->getBody());
                $token = $data->data->remember_token;
                session(['token'=> $token]);
            });
            $promise->wait();
            $my_token = session('token');
            //dd($my_token);

            $user->remember_token = $my_token;
            session(['user' => $user]);
            Cookie::queue('token', session('token'));
            //Auth::guard('user')->login($user_account[0]);
            //return redirect()->to('http://127.0.0.1:8000/login');
            return redirect('/login');
        }else{
            $user_account=User::create([
                'name' => $name,
                'email'=> $email,
                'password'=> bcrypt('12345678'),
                'provider_user_id'=>$provider_user_id,
                'provider'=>$provider,
                'role_id'=> 9,
                'cp_id' =>1,
                'active'=>1
            ]);

            //dd($user_account);
            //Auth::guard('user')->login($user_account);
            //return redirect('/login');
            //return redirect()->to('http://127.0.0.1:8000/login');
            return redirect('/login');
        }
    }
}
