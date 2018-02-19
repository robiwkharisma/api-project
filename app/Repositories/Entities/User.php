<?php

namespace App\Repositories\Entities;

use Illuminate\Auth\Authenticatable;
// use Illuminate\Database\Eloquent\Model;
use Cartalyst\Sentinel\Users\EloquentUser as Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\SoftDeletes;

use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

use Mail;
use Illuminate\Support\Facades\Hash;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract, Transformable
{
    const UNREGISTERED_USER_MESSAGE = 'UserUnregistered';
    const NOT_ALLOWED_UPDATE_USER_MESSAGE = 'NotAllowedUpdateUserDifferentCompany';
    const PROMOTE_NOT_ALLOWED = 'NotAllowedPromoteUser';
    const DEMOTE_NOT_ALLOWED = 'NotAllowedDemoteUser';
    const EXPIRED_CODE = 'ExpiredCode';
    const INVALID_PASSWORD_CONFIRM  = 'InvalidPasswordConfirm';
    const ADMIN_TYPE  = 'admin';
    const CUSTOMER_TYPE  = 'customer';

    use Authenticatable, CanResetPassword;
    // use SoftDeletes;
    use TransformableTrait;

    private $user;
    private $userSentinel;

    /**
     * {@inheritDoc}
     */
    protected $dates = ['deleted_at'];

    protected $table = 'users';

    protected $fillable = [
        'email',
        'first_name',
        'is_active',
        'is_customer',
        'is_guest',
        'job_title',
        'last_name',
        'mobile_phone',
        'office_phone',
        'password',
        'profile_img',
        'reset_token',
    ];

    public function isGuest()
    {
        return $this->is_guest ?: FALSE;
    }

    public function isCustomer()
    {
        return $this->is_customer ?: FALSE;
    }

    public function isActive()
    {
        return $this->is_active ?: FALSE;
    }

    /**
     * Set attributes from JSON
     *
     * @param mixed $attributes
     * @return void
     */
    public function setAttributeFromJson($attributes)
    {
        if (isset($attributes['isCustomer']))
            $this->is_customer = (bool) $attributes['isCustomer'];

        if (isset($attributes['isActive']))
            $this->is_active = (bool) $attributes['isActive'];

        if (isset($attributes['isGuest']))
            $this->is_guest = (bool) $attributes['isGuest'];

        if (isset($attributes['firstName']))
            $this->first_name = $attributes['firstName'];

        if (isset($attributes['lastName']))
            $this->last_name = $attributes['lastName'];

        if (isset($attributes['email']))
            $this->email = $attributes['email'];

        if (isset($attributes['jobTitle']))
            $this->job_title = $attributes['jobTitle'];

        if (isset($attributes['mobilePhone']))
            $this->mobile_phone = $attributes['mobilePhone'];

        if (isset($attributes['officePhone']))
            $this->office_phone = $attributes['officePhone'];

        if (isset($attributes['profilePicPath']))
            $this->profile_img = $attributes['profilePicPath'];
    }

    /**
     * Set Active Flag
     *
     * @param boolean $bool
     * @return void
     */
    public function setActive($bool = TRUE)
    {
        $this->is_active = $bool;
    }

    public function getUser()
    {
        if ($this->user)
        {
            return $this->user;
        }

        $this->user = $this;

        return $this;
    }

    public function getUserSentinel()
    {
        if ($this->userSentinel)
        {
            return $this->userSentinel;
        }

        $this->userSentinel = \Sentinel::findById($this->id);

        return $this->userSentinel;
    }

    /**
     * Register a User and Activate
     *
     * @param mixed $attributes
     * @return void
     */
    public function registerAndActivate($attributes)
    {
        $password = isset($attributes['password']) ? $attributes['password'] : null;

        $credentials = [
            'email'    => $attributes['email'],
            'password' => generate_password($password),
        ];

        // DEVNOTE: store passowrd in log if local
        if (\App::environment(['stage', 'local', 'dev', 'testing']))
            \Log::info($credentials);

        $this->userSentinel = \Sentinel::registerAndActivate($credentials);

        $user = $this->find($this->userSentinel->id);
        $user->setAttributeFromJson($attributes);
        $user->save();

        $find_data = $user->toArray();
        $find_data['password'] = $credentials['password'];

        // Notify new password via email
        if (!\App::environment(['local', 'testing']))
            Mail::send('emails.user_password', $find_data, function($message) use($find_data) {
                $message->from("noreply@apiproject.com", 'No-Reply');
                $message->to($find_data['email'], $find_data['first_name'])->subject('Password User');
            });

        $this->user = $user;

        return $user;
    }

    /**
     * Assign Role by role slug
     *
     * @param string $roleSlug
     * @return void
     */
    public function assignRoleByRoleSlug($roleSlug)
    {
        $role = \Sentinel::findRoleBySlug($roleSlug);

        if ($role)
            $role->users()->attach($this->getUserSentinel());
    }

    /**
     * Set User Password
     *
     * @param string $password
     * @return void
     */
    public function setPassword($password=null)
    {
        $this->password = Hash::make(generate_password($password));

        return $password;
    }

    /**
     * Set reset token
     *
     * @param string $code
     * @return void
     */
    public function setCode($code=null)
    {
        $this->reset_token = $code;

        return $code;
    }
}
