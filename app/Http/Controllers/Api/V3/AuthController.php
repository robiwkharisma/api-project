<?php

namespace App\Http\Controllers\Api\V3;

use App\Http\Controllers\Api\ApiBaseController as Controller;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use Validator;

use App\Repositories\Auth\Contract\AuthRepository;
use App\Repositories\Entities\User;

use Tymon\JWTAuth\JWTAuth;
use Mail;
use Hash;

use Illuminate\Database\Eloquent\ModelNotFoundException;

class AuthController extends Controller
{
    private $repositoryAuth;
    private $JWTAuth;

    public function __construct(AuthRepository $repositoryAuth, JWTAuth $JWTAuth)
    {
        $this->repositoryAuth = $repositoryAuth;
        $this->JWTAuth = $JWTAuth;
    }

    /**
     * resetInitiate
     * @param  mixed $request
     * @return [oid]           
     */
    public function resetInitiate(Request $request)
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
                $result = $this->repositoryAuth->resetPassword($param);
                $this->data    = ['email' => $request->get('email')];
                $this->message = trans('general.success');
            }
            catch (\Exception $e)
            {
                $this->exception = $e;
                $this->data    = ['email' => $request->get('email')];
                $this->error   = TRUE;
                $this->code    = self::CODE_9001;
                $this->message = $e->getMessage();
            }
        }

        return $this->response();
    }

    public function resetChange(Request $request)
    {
        $param = $request->all();

        try
        {
            $user = $this->repositoryAuth->changePassword($param);
            Sentinel::login($user);
            $token = $this->JWTAuth->fromUser($user);
            $result = $this->repositoryAuth->getLoginData($token);
            $this->data    = $result;
            $this->message = trans('general.success');
        }
        catch (\Exception $e)
        {
            $this->exception = $e;
            $this->error   = TRUE;
            $this->code    = self::CODE_9001;
            $this->message = $e->getMessage();
        }

        return $this->response();      
    }
}