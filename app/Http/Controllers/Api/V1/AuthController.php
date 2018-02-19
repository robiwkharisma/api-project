<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiBaseController as Controller;
use Illuminate\Http\Request;
use Validator;

use Tymon\JWTAuth\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

use App\Repositories\Auth\Service\AuthRepositorySentinel as AuthService;
use App\Repositories\Repository\UserRepositoryEloquent as UserRepository;
use App\Repositories\Entities\User;

// Hash, TODO move to UserRepository
use Illuminate\Support\Facades\Hash;
// Mail, TODO move to MailRepository
use Mail;

use Illuminate\Database\Eloquent\ModelNotFoundException;

class AuthController extends Controller
{
    private $authService;
    private $JWTAuth;
    private $model_user;

    public function __construct(AuthService $authService, JWTAuth $JWTAuth,
                                UserRepository $model_user)
    {
        $this->authService = $authService;
        $this->JWTAuth = $JWTAuth;
        $this->model_user = $model_user;
    }

    public function login(Request $request)
    {
        $param = $request->all();

        // basic init
        $this->error = TRUE;
        $this->message = trans('general.login_error');

        $rules = array(
            'email'    => 'required|email',
            'password' => 'required'
        );

        $validate = Validator::make($param,$rules);

        if ($validate->fails())
        {
            $this->code = self::CODE_9101;
            $this->data = $validate->errors();
        }
        else
        {
            $credentials = $request->only(['email', 'password']);
            try {
                $token = $this->JWTAuth->attempt($credentials);

                if (!$token)
                {
                    $this->code = self::CODE_9101;
                    $this->data = $token;
                }
                else
                {
                    // Get User Data
                    $userData = $this->authService->getLoginData($token);

                    // success response setup
                    $this->code = self::CODE_0;
                    $this->data = $userData;

                    $this->error   = FALSE;
                    $this->message = trans('general.login_success');
                }
            } catch (JWTException $e) {
                $this->exception = $e;
                $this->code = self::CODE_9001;
                $this->error   = TRUE;
                $this->message = $e->getMessage();
            } catch (\Exception $e) {
                $this->exception = $e;
                $this->code = self::CODE_9001;
                $this->error   = TRUE;
                $this->message = $e->getMessage();
            }
        }

        return $this->response();
    }

    public function logout(Request $request)
    {
        $param = $request->all();
        $rules = array(
            'email'    => 'required|email'
        );

        $validate = Validator::make($param,$rules);

        if ($validate->fails())
        {
            $this->code = self::CODE_9101;
            $this->data = $validate->errors();
        }
        else
        {
            try 
            {
                // get token user
                $token = $this->JWTAuth->getToken();
                // get id user
                $dataUser  = user_info('id');
                // delete token user
                $deleteToken = $this->JWTAuth->invalidate($token);

                $this->code = self::CODE_0;
                $this->data = $dataUser;
                $this->error   = FALSE;
                $this->message = trans('general.success');

            } catch (JWTException $e) {
                $this->exception = $e;
                $this->code = self::CODE_9001;
                $this->error   = TRUE;
                $this->message = $e->getMessage();
            }
        }

        return $this->response();
    }

    public function reset(Request $request)
    {
        $param = $request->all();

        // basic init
        $this->error = TRUE;
        $this->message = trans('general.login_error');

        $rules = array(
            'email'    => 'required|email',
        );

        $validate = Validator::make($param,$rules);

        if ($validate->fails())
        {
            $this->code = self::CODE_9101;
            $this->data = $validate->errors();
        }
        else
        {
            try
            {
                // TODO: Do this on repository level!

                if($user = User::where('email', $param['email'])->first()){
                    $user = $this->model_user->find($user->id);
                    // $user = $user['data'];

                    // Generate new password
                    // TODO: Make function to generate random password in User Repository
                    $password = "";
                    for ($i = 0; $i<8; $i++) {
                        $password .= mt_rand(0,9);
                    }

                    // Update User
                    $this->model_user->update(['password' => Hash::make($password)], $user['id']);

                    $find_data = $user;
                    $find_data['password'] = $password;

                    // Notify new password via email
                    // TODO: Make Email repository
                    // $test = TRUE;
                    // if ($test)
                        \Log::info($find_data);

                    // if (!$test)
                        Mail::send('emails.password', $find_data, function($message) use($find_data) {
                                $message->from("noreply@myre.com", 'No-Reply');
                                $message->to($find_data['email'], $find_data['firstName'])->subject('Password User');
                            });

                    // success response setup
                    $this->code = self::CODE_0;
                    $this->data = $param;
                    $this->error   = FALSE;
                    $this->message = trans('general.reset_password_success');

                }else{
                    $this->code = self::CODE_9001;
                    $this->data = $param;
                    $this->error   = FALSE;
                    $this->message = User::UNREGISTERED_USER_MESSAGE;
                }
            }
            catch (ModelNotFoundException $e)
            {
                $this->exception = $e;
                $this->code    = self::CODE_9001;
                $this->error   = TRUE;
                $this->message = "Invalid-User";
            }
            catch (\Exception $e)
            {
                $this->exception = $e;
                $this->code    = self::CODE_9001;
                $this->error   = TRUE;
                $this->message = $e->getMessage();
            }
        }

        return $this->response();
    }
}