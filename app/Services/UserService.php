<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Yish\Generators\Foundation\Service\Service;

class UserService extends Service
{
    protected $repository;

    public function __construct(UserRepository $repository) {
        $this->repository = $repository;
    }

    public function getValidator($input)
    {
        $rules = [
            'email' => 'required',
            'password' => 'required|between:6,20'
        ];
        $msg = [
            'email.required' => '信箱不能為空',
            'password.required' => '密碼不能為空',
            'password.between' => '密碼須介於6~20位',
        ];

        return Validator::make($input, $rules, $msg);
    }

    public function getEmailValidator($input)
    {
        $rules = [
            'email' => 'required',
        ];
        $msg = [
            'email.required' => '信箱不能為空'
        ];

        return Validator::make($input, $rules, $msg);
    }

    public function getRegisterValidator($input)
    {
        $rules = [
            'name'  => 'required',
            'email' => 'required',
            'password' => 'between:6,10|required_with:password_confirmation|same:password_confirmation',
            //'password_confirmation' => 'between:6,10'
        ];
        $msg = [
            'name.required' => '用戶姓名不能為空',
            'email.required' => '信箱不能為空',
            'password.between' => '密碼須介於6~10位',
            'password.required_with' => '密碼跟確認碼不能為空',
            'password.same' => '密碼跟確認碼必須相同',
            //'password_confirmation.between' => '確認碼須介於6~10位',
        ];

        return Validator::make($input, $rules, $msg);
    }

    public function checkPassword($user, $password)
    {
        try {
            if (Hash::check($password, $user->password))
            {
                return true;
            } else {
                return false;
            }
        } catch (DecryptException $err) {
            echo $err;
            return false;
        }
    }

    public function getUserByEmail($email)
    {
        try {
            $user = $this->repository->firstBy('email', $email);
            if ($user)
            {
                return $user;
            } else {
                return null;
            }
        } catch (DecryptException $err) {
            echo $err;
            return null;
        }
    }

    public function getToken($input)
    {
        try {
            $uri = env('APP_URL') . '/users/login';
            $client = new \GuzzleHttp\Client(['verify' => false]);
            $request = new \GuzzleHttp\Psr7\Request('POST', $uri, ['Content-type' => 'application/json'], json_encode($input));
            $token = '';
            $promise = $client->sendAsync($request)->then(function ($response) {
                $data = json_decode($response->getBody());
                $token = $data->data->remember_token;
                session(['token' => $token]);
            });
            $promise->wait();
            return session('token');
        } catch (DecryptException $err) {
            echo $err;
            return null;
        }
    }

    public function getUserByToken($token)
    {
        try {
            $uri = env('APP_URL').'/users/checkToken';
            $client = new \GuzzleHttp\Client(['verify' => false]);
            $request = new \GuzzleHttp\Psr7\Request('POST', $uri, ['Content-type' => 'application/json'], json_encode(['token'=>$token]) );
            $promise = $client->sendAsync($request)->then(function ($response) {
                $result = json_decode($response->getBody());
                if($result->code == 200) {
                    $data = $result->data;
                    $user = User::find($data->id);
                    if($user) {
                        $user->remember_token = session('token');
                    }

                    session(['user' => $user]);
                }
            });
            $promise->wait();

            return session('user');
        } catch (DecryptException $err) {
            echo $err;
            return null;
        }
    }

    public function sendMailCheck($email)
    {
        try {
            $uri = env('APP_URL').'/users/mailCheck';
            $client = new \GuzzleHttp\Client(['verify' => false]);
            $request = new \GuzzleHttp\Psr7\Request('POST', $uri, ['Content-type' => 'application/json'], json_encode(['email'=>$email]) );
            $promise = $client->sendAsync($request)->then(function ($response) {
                $result = json_decode($response->getBody());
                if($result->code == 200) {
                    $data = $result->data;
                    $user = User::find($data->id);
                    if($user) {
                        $user->remember_token = session('token');
                    }

                    session(['user' => $user]);
                }
            });
            $promise->wait();

            return session('user');
        } catch (DecryptException $err) {
            echo $err;
            return null;
        }
    }


}
