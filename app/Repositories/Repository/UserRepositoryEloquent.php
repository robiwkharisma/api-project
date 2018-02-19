<?php

namespace App\Repositories\Repository;

use Illuminate\Container\Container as Application;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

use App\Repositories\Contracts\UserRepository;
use App\Repositories\Entities\User;
use App\Repositories\Validators\UserValidator;

use Tymon\JWTAuth\JWTAuth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Illuminate\Support\Facades\Hash;

use Response;
use Validator;
use DB;
use Input;
use Mail;
use Sentinel;

class UserRepositoryEloquent extends BaseRepository implements UserRepository
{
    public $version = null;
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return User::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * Repository presenter
     */
    public function presenter()
    {
        return 'App\\Repositories\\Presenters\\UserPresenter';
    }

	/**
	 * @var $model
	 */
	private $model_user;

	public function __construct(
                                User $model_user,
                                Application $app
                                )
	{
        $this->app = $app;
        parent::__construct($app);

		$this->model_user          = $model_user;
	}

    /**
     * Toggle User Active Flag
     *
     * @param integer $id
     * @return void
     */
    public function toggleActive($id)
    {
        $user = User::findOrFail($id);

        $user->is_active = !(bool) $user->is_active;
        $user->save();

        return $this->find($id);
    }

    /**
     * Toggle User Customer Flag
     *
     * @param integer $id
     * @return void
     */
    public function toggleCustomer($id)
    {
        $user = User::findOrFail($id);

        $user->is_customer = !(bool) $user->is_customer;
        $user->save();

        return $this->find($id);
    }

    /**
     * Check registered user email
     *
     * @param string $email
     * @return void
     */
    public function checkRegisteredEmail($email)
    {
		$data = $this->model_user->where('email',$email)->get()->first();
        $available = count($data);

        return (bool) $available;
	}

    /**
     * Get User List
     *
     * @return void
     */
    public function getList()
    {
        $this->pushCriteria(app('App\Repositories\Criteria\OrderByIdDescCriteria'));
        
        $userList = $this->all();

        return $userList;
    }

}