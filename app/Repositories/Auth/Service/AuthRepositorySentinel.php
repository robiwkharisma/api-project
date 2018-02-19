<?php

namespace App\Repositories\Auth\Service;

use Exception;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Cartalyst\Sentinel\Users\UserInterface;

use App\Repositories\Auth\Contract\AuthRepository;

use App\Repositories\Repository\UserRepositoryEloquent as UserRepository;
use App\Repositories\Entities\User;

use Mail;
use DB;

/**
 * Class AuthRepositorySentinel
 * @package namespace App\Repositories\Auth\Service;
 */
class AuthRepositorySentinel implements AuthRepository
{
    private $user = null;
	/**
	 * @var $model
	 */
	private $repository_user;
	private $model_user;

	public function __construct(
                                UserRepository $repository_user,
                                User $model_user
                                )
	{
        $this->repository_user = $repository_user;
        $this->model_user = $model_user;
	}

    /**
     * Check a user's credentials
     *
     * @param  array  $credentials
     * @return bool
     */
    public function byCredentials(array $credentials = [])
    {
        try {
            $user = Sentinel::stateless($credentials);

            if (!$user->is_active) throw new \Exception("Account is inactive.", 9001);

            return $user instanceof UserInterface;
        } catch (Exception $e) {
            if ($e->getCode() == 9001) throw $e;

            return false;
        }
    }

    /**
     * Authenticate a user via the id
     *
     * @param  mixed  $id
     * @return bool
     */
    public function byId($id)
    {
        try {
            $user = Sentinel::findById($id);
            Sentinel::login($user);
            return $user instanceof UserInterface && Sentinel::check();
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Get the currently authenticated user
     *
     * @var boolean $cache Get from cache/as previously set attribute.
     * @return mixed
     */
    public function user($cache = TRUE)
    {
        if ($cache == TRUE && !empty($this->user))
            return $this->user;

        $this->user = Sentinel::getUser();
        return $this->user;
    }

    public function getListArrayPermissions($profile)
    {
        $permissions = [];
        if (!empty($profile)) {
            $getPermission = json_decode($profile->permissions);
            foreach ($getPermission as $dataPermission) {
                    array_push($permissions, key($dataPermission));
            }
        }

        return $permissions;
    }

    /**
     * Get role slug list from current User
     *
     * @return mixed
     */
    public function getRoles()
    {
        $user = $this->user();

        $roles = [];

        if ($user)
        {
            $userRoles = $user->roles;
            if (count($userRoles))
            {
                foreach ($userRoles as $role) {
                    $roles[] = $role->slug;
                }
            }
        }

        return $roles;
    }

    public function getUserType()
    {
        $status = Sentinel::check()->is_customer;

        // TODO
        // Devnote:
        // User Type checking determined from `is_customer` attribute.
        // Can use: Sentinel::check()->is_customer

        if ($status) {
            $userType = "customer";
        } else {
            $userType = "admin";
        }

        return $userType;
    }

    public function getLoginData($token)
    {
        $user = $this->user();

        $re['userType'] = $this->getUserType();
        $re['token']    = $token;

        $this->repository_user->setPresenter('App\\Repositories\\Presenters\\V2UserPresenter');
        $re['user']     = $this->repository_user->find($user->id);

        return $re;
    }

    /**
     * Get user Info
     *
     * @param string $column
     * @return void
     */
    public function getUserInfo($column = null)
    {
        $user = $this->user();

        if ($user)
        {
            if (is_null($column)) {
                return $user;
            }

            if ($column == 'role') {
                return $this->getRoles();
            }

            return $user->{$column};
        }

        return null;
    }

    /**
     * Get User Token
     *
     * @param mixed $user can be User object
     * @return void
     */
    public function resetPassword($attributes, $isNewUser=FALSE)
    {
        try {
            DB::beginTransaction();
            
            if($user = User::where('email', $attributes['email'])->first()){
                $user = $this->model_user->find($user->id);

                $resetToken = bin2hex(random_bytes(32));
                $user->setCode($resetToken);
                $user->save();

                $webBaseUrl = \Config::get('app_settings.web-base-url');
                
                $sentUserData = [
                    'firstName'     => $user->first_name,
                    'resetToken'    => $webBaseUrl.'reset-change?code='.$resetToken,
                ];

                if($isNewUser){
                    Mail::to($user->email)->queue(new \App\Mail\SetPasswordUser($sentUserData));
                }else{
                    Mail::to($user->email)->queue(new \App\Mail\ResetPassword($sentUserData));
                }

            }else{
                throw new \Exception(User::UNREGISTERED_USER_MESSAGE);
            }

            DB::commit();

        } catch (Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage(), null, $e);
        }
        
        return TRUE;
    }

    /**
     * changePassword
     *
     * @param mixed $attributes
     * @return void
     */
    public function changePassword($attributes)
    {
        try {
            DB::beginTransaction();
            
            if($user = User::where('reset_token', $attributes['code'])->where('reset_token','!=',NULL)->first()){
                if($attributes['password'] != $attributes['passwordConfirm']){
                    throw new \Exception(User::INVALID_PASSWORD_CONFIRM);    
                }
                // Update password
                $user->setPassword($attributes['password']);
                $user->reset_token = NULL;
                $user->save();
            }else{
                throw new \Exception(User::EXPIRED_CODE);
            }

            DB::commit();

        } catch (Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage(), null, $e);
        }
        
        return $user;
    }
}
