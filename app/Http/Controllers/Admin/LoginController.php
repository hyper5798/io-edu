<?php

namespace App\Http\Controllers\Admin;

use App\Constant\CacheConstant;
use App\Constant\UserConstant;
use App\Mail\SendTokenMail;
use App\Mail\SendVerifyMail;
use App\Models\Cp;
use App\Models\Role;
use App\Models\User;
use App\Repositories\AnnounceRepository;
use App\Repositories\MemberRepository;
use App\Repositories\PasswordResetRepository;
use App\Services\Base\Interfaces\CacheServiceInterface;
use App\Services\Base\Services\CacheService;
use App\Services\SettingService;
use App\Services\UserService;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use App\Jobs\SendTokenEmailJob;
use App\Models\Field;
use App\Models\Level;

class LoginController extends CommonController
{
    private $memberRepository, $userService, $passwordResetRepository, $settingService;
    protected $announceRepository;
    /**
     * @var CacheService
     */
    protected $cacheService;
    /**
     * LoginController constructor.
     * @param PasswordResetRepository $passwordResetRepository
     * @param MemberRepository $memberRepository
     * @param UserService $userService
     * @param CacheServiceInterface $cacheService
     * @param AnnounceRepository $announceRepository
     * @param SettingService $settingService
     */
    public function __construct(
        PasswordResetRepository $passwordResetRepository,
        MemberRepository $memberRepository,
        UserService $userService,
        CacheServiceInterface $cacheService,
        SettingService $settingService,
        AnnounceRepository $announceRepository
    )

    {
        $this->memberRepository = $memberRepository;
        $this->userService = $userService;
        $this->cacheService = $cacheService;
        $this->passwordResetRepository = $passwordResetRepository;
        $this->settingService = $settingService;
        $this->announceRepository = $announceRepository;
    }

    /**
     * User sign in.
     *
     * @param Request $request
     * @return RedirectResponse|View
     */
    public function login(Request $request)
    {
        //$input = $request->all();

        $link = $request['link'];
        if($link == null) {
            $link = 'all';
        }
        Cookie::queue('link', $link);
        $token = request()->cookie('token');
        //從token反取user
        if($token && $token != '') {
            $user = $this->userService->getUserByToken($token);

            if($user) {
                $ip = $request->ip();
                saveUserData($user, $this->cacheService, $ip);
                $path = '/node/myDevices?link=develop';
                return redirect($path);
            }
        }
        /*$fields = Field::where('isAll',0)->get();
        $levels = Level::all();
        session(['fields'=> $fields]);
        session(['levels'=> $levels]);*/

        return view('pages.login', compact(['link']));
    }

    /**
     * User sign in.
     *
     * @param Request $request
     * @return RedirectResponse|View
     */
    public function postLogin(Request $request)
    {
        $link = $request['link'];
        $ip = $request->ip();
        if($link == null) {
            $link = 'all';
        }
        $input = $request->all();
        /*$cp_id = request()->cookie('cp_id');
        if($cp_id != null) {
            $cp_id = (int)$cp_id;
        }*/

        //輸入格式驗證
        $validator = $this->userService->getValidator($input);

        if(count($validator->errors()->all()) > 0){
            session(['error'=> $validator->errors()]);
            return back()->withErrors($validator);
        }

        //Email驗證
        $loginUser = $this->userService->firstBy('email', $input['email']);

        $login_ip = getUserCache($this->cacheService, $loginUser->id,  'ip');
        if($login_ip && $ip !=  $login_ip && $loginUser->role_id>3) {
            return back()->withErrors('此帳號已經在另一台電腦登入，請勿重複登入!');
        }

        if ($loginUser == null)
            return back()->withErrors('無此帳號，請重新輸入');
        if($loginUser->active == 0)
            return back()->withErrors('電子信箱尚未驗證，請重送認證信或到你註冊信箱啟用帳號。');
        //密碼驗證
        if (!$this->userService->checkPassword($loginUser, $input['password'])) {
            return back()->withErrors('密碼錯誤');
        }

        $role = null;

        $loginUser->remember_token = $this->userService->getToken($input);
        saveUserData($loginUser, $this->cacheService, $ip);

        if($loginUser) {


            $path = '/node/myDevices?link=develop';
            return redirect($path);

        }
        return view('pages.login', compact(['link']));
    }

    /**
     * User sign up.
     *
     * @param Request $request
     * @return \Illuminate\Routing\Redirector|View
     */
    public function register(Request $request)
    {
        $link = $request['link'];
        if($link == null) {
            $link = 'all';
        }
        $email = $request['email'];
        $input = $request->all();
        $announce = $this->announceRepository->findBy('tag', UserConstant::ANNOUNCE_DISCLAIMER);
        if ($email!= null ) {

            $request->flash();
            $validator = $this->userService->getRegisterValidator($input);

            if(count($validator->errors()->all()) > 0){
                session(['error'=> $validator->errors()]);
                $path = '/register?link='.$link;
                return redirect($path)
                    ->withErrors($validator)
                    ->with( ['announce' =>  $announce] )
                    ->withInput();
                //return redirect('register')->withInput($request->all());
            }
            $user = $this->userService->firstBy('email', $email);
            if($user && $user->active == 1) {
                $path = '/register';
                return redirect($path)
                    ->withErrors(trans('auth.email_exist'))
                    ->with( ['announce' =>  $announce] )
                    ->withInput();
            } else if($user && $user->active == 0) {
               $this->userService->update($user->id,[
                    'name' => $input['name'],
                    'password' => bcrypt( $input['password']),
                ]);
            }  else  {//New active:0
                $user = $this->userService->create([
                    'name' => $input['name'],
                    'email' => $input['email'],
                    'password' => bcrypt( $input['password']),
                    'cp_id' => 1,
                    'role_id' => UserConstant::NORMAL_USER
                ]);
            }

            //$data = $this->userService->sendMailCheck($email);
            $input['id'] = $user['id'];
            $this->setAndSendMail($input);

            $path = '/show-verify-email?email='.$input['email'];
            return redirect($path)->with( ['announce' =>  $announce] );

        } else {
            return view('pages.register', compact(['link', 'announce']));
        }
    }

    public function resendEmail(Request $request) {
        $input = $request->all();
        $user = $this->userService->firstBy('email', $input['email']);
        $input['id'] = $user['id'];
        $input['name'] = $user['name'];
        $count = $this->setAndSendMail($input);
        if($count>UserConstant::RESEND_MAIL_MAX) {
            return redirect('/login')->withErrors('你已寄送超過10次認證信，請更換電子郵件再註冊!');
        }

        $path = '/show-verify-email?email='.$input['email'].'&count='.$count;
        //超過
        if($count > UserConstant::RESEND_MAIL_CHECK)
            return redirect($path)->withErrors('你已寄送多次認證信，請檢查電子信箱垃圾桶或信箱設定!');
        return redirect($path);
    }

    function setAndSendMail($input) {
        $count = $this->settingService->saveMailSetting($input['id']);
        //最多重寄次數
        if($count <= UserConstant::RESEND_MAIL_MAX) {
            $data = [
                'subject'=>'啟用帳戶電子信箱確認',
                'to_email' => $input['email'],
                'to_name' => $input['name'],
                'msg'=>'message',
                'url' => url('/active-account?id='.$input['id'])
            ];
            $emailData = getEmailData($data);
            //sendMailTest($emailData);
            //訊息寫入信件視圖
            //$email = new SendVerifyMail($emailData);
            //sendMail($email, $input['email']);
            sendRegisterMail($emailData);
            //$path = '/login?link='.$link;
            //return redirect($path)->withInput();*/
        }
        return $count;
    }

    /**
     * Display verify email message.
     * @param Request $request
     * @return View
     */
    public function showVerifyEmail(Request $request)
    {
        $count = (int)$request->input('count', 0);
        $email = $request->input('email');
        $verify = $this->userService->firstBy('email', $email);
        return view('pages.show-verify-email', ['verify'=>$verify]);
    }

    /**
     * Active account.
     * @param Request $request
     * @return \Illuminate\Routing\Redirector
     */
    public function activeAccount(Request $request)
    {
        $id = (int)$request->input('id', 0);
        $this->userService->update($id,['active'=>1, 'email_verified_at'=>now()]);

        return redirect('/login');
    }

    /**
     * User sign out.
     * @param Request $request
     * @return RedirectResponse
     */
    public function quit(Request $request)
    {
        $user = session('user');
        $user = User::find($user['id']);
        pullUserData($user['id'], $this->cacheService);

        $user->remember_token = null;
        $user->save();
        $link = $user['link'];
        if($link == null) {
            $link = 'all';
        }
        Cookie::queue('token', null);
        session(['user'=>null]);
        session(['target'=>null]);
        //$path = '/login?link='.$link;
        $path = '/';
        return redirect($path);
    }

    /**
     * Verify user password.
     *
     * @param int $id
     * @param string $form_password
     * @return bool
     */
    private function checkPass($id, $form_password)
    {
        $user = DB::table('users')->find($id);
        if (Hash::check($form_password, $user->password))
        {
            return true;
        } else {
            return false;
        }
    }
    /**
     * User forgot password.
     *
     * @return View
     */
    public function forgotPassword()
    {
        return view('pages.forgot-password');
    }

    /**
     * Check email exist or not then send token.
     * @param Request $request
     * @return RedirectResponse|View
     */
    public function forgotPasswordCheck(Request $request)
    {
        //電子信箱檢查存在與否
        $input = $request->all();
        $validator = $this->userService->getEmailValidator($input);

        if(count($validator->errors()->all()) > 0){
            session(['error'=> $validator->errors()]);
            return back()->withErrors($validator);
        }
        $to_email = $request->input('email');
        $user = $this->userService->getUserByEmail($to_email);
        if($user == null) {
            return back()->withErrors('查無此信箱!');
        }

        //清除密碼重設token
        $items = $this->passwordResetRepository->getBy('email', $to_email);
        if($items->count()>0) {
            foreach ($items as $item) {
                $item->delete();
            }
        }
        //密碼重設token存DB
        $token = getToken();
        $this->passwordResetRepository->create(['email'=>$to_email, 'token'=>$token, 'created_at'=>now()]);

        //信件的內容(即表單填寫的資料)
        $data = [
            'subject'=>'您的 ioEDU驗證碼為 '.$token,
            'to_email' => $to_email,
            'to_name' => $user->name,
            'msg'=>'message',
            'url' => url('/token-send')
        ];
        $emailData = getEmailData($data);
        //訊息寫入信件視圖
        //$email = new SendTokenMail($emailData);
        //sendMail($email, $to_email);
        sendForgotMail($emailData);

        return redirect('/token-send?id='.$user->id);

    }

    /**
     * User sign out.
     * @param Request $request
     * @return RedirectResponse|View
     */
    public function tokenVerify(Request $request)
    {
        //顯示點選電子信箱連結或直接輸入認證碼
        $id = (int)$request->input('id', 0);
        $verify = $this->userService->find($id);
        return view('pages.token-send', compact('verify'));
    }

    /**
     * Token check and redirect to modify password.
     * @param Request $request
     * @return RedirectResponse
     */
    public function tokenCheck(Request $request) {
        $token = $request->input('token', null);
        if($token == null) return back()->withErrors(['未輸入驗證碼!']);

        $reset = $this->passwordResetRepository->firstBy('token', $token);
        if($reset == null) return back()->withErrors(['驗證不正確!']);
        $carbon = carbon::parse ($reset->created_at);
        $int = (new Carbon)->diffInSeconds ($carbon, true);
        if($int>600) {
            //return back()->withErrors('驗證碼過期!');
            return Redirect::to('/forgot-password')
                ->withErrors('驗證碼過期!');
        }
        $user = $this->userService->findBy('email', $reset->email);
        saveUserData($user, $this->cacheService);

        return redirect('/pass?page=node&code=false');

    }
}


function checkToken($token) {
    if($token && $token != '') {
        $uri = env('APP_URL') . '/users/checkToken';
        $client = new \GuzzleHttp\Client(['verify' => false]);
        $request = new \GuzzleHttp\Psr7\Request('POST', $uri, ['Content-type' => 'application/json'], json_encode(['token' => $token]));
        $test = false;
        $promise = $client->sendAsync($request)->then(function ($response) {
            $data = json_decode($response->getBody());

            if ($data->code == 200) {

                $user = User::find($data->data->id);

                $role = Role::where('role_id', $user->role_id)->first();
                $user->remember_token = session('token');
                //$user->save();
                $user->role_name = $role->role_name;
                session(['user' => $user]);
                session(['result' => true]);

            } else {
                session(['result' => false]);
            }
        });
        $promise->wait();
        return session('result');
    }
}

/**
 * Generate api key by app id and team id.
 *
 * @param integer $app_id
 * @param integer id
 *  @return string api key
 */
function getToken(){

    $key = '';
    $word = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';//字典檔
    $len = strlen($word);//取得字典檔長度

    for($i = 0; $i < 8; $i++){ //總共取 幾次.
        $key .= $word[rand() % $len];//隨機取得一個字元
    }

    return $key;//回傳亂數帳號
}

function saveUserData($user, $cacheService, $ip) {

    $target = ['cp_id'=>$user->cp_id, 'user_id'=>$user->id];
    session(['user'   => $user]);
    session(['target' => $target]);
    setUserCache($cacheService, $user['id'], 'cp_id', $user->cp_id);
    setUserCache($cacheService, $user['id'], 'user_id', $user['id']);
    setUserCache($cacheService, $user['id'], 'token', $user->remember_token);
    setUserCache($cacheService, $user['id'], 'ip', $ip);
    Cookie::queue('token', session('token'));
}

function pullUserData($user, $cacheService) {

    forgotUserCache($cacheService, 'id_'.$user['id'].':cp_id');
    forgotUserCache($cacheService, 'id_'.$user['id'].':user_id');
    forgotUserCache($cacheService, 'id_'.$user['id'].':token');
    forgotUserCache($cacheService, 'id_'.$user['id'].':ip');
}

function sendMail(Mailable $mail, $to_mail) {
    dispatch(new SendTokenEmailJob($mail, $to_mail));
}

function sendRegisterMail($data) {
    Mail::send('pages.verifyEmail', $data, function($message) use ($data) {
        $message->from($data['from_email'], $data['from_name']);
        $message->to($data['to_email'], $data['to_name'])->subject($data['subject']);
    });
}

function sendForgotMail($data) {
    Mail::send('pages.tokenEmail', $data, function($message) use ($data) {
        $message->from($data['from_email'], $data['from_name']);
        $message->to($data['to_email'], $data['to_name'])->subject($data['subject']);
    });
}

