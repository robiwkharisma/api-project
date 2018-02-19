<?php

namespace App\Repositories\Transformers;

use League\Fractal\TransformerAbstract;
use App\Repositories\Entities\User;
use App\Repositories\Entities\Company;
use App\Repositories\Entities\UserCustomer;
use App\Repositories\Entities\Customer;
use App\Repositories\Entities\Role;
use App\Repositories\Entities\UserHasRole;

use League\Fractal\Manager;
use League\Fractal\Serializer\DataArraySerializer;

/**
 * Class UserTransformer
 * @package namespace App\Repositories\Transformers;
 */
class UserTransformer extends TransformerAbstract
{

    protected $defaultIncludes = [
        'company',
    ];

    /**
     * Transform the \User entity
     * @param \User $model
     *
     * @return array
     */
    public function transform(User $model)
    {
        return [
            'id'            => (int) $model->id,
            'civility'      => $model->civility,
            'lastName'      => $model->last_name,
            'firstName'     => $model->first_name,
            'email'         => $model->email,
            'mobilePhone'   => $model->mobile_phone,
            'officePhone'   => $model->office_phone,
            'jobTitle'      => $model->job_title,
            'isGuest'       => (bool) $model->isGuest(),
            'isCustomer'    => (bool) $model->isCustomer(),
            'isActive'      => (bool) $model->isActive(),
            'profilePicPath' => $model->profile_img,
            'customers'     => $this->customers($model),
            'roles'         => $this->roles($model),
            'companyList'   => $this->companyList($model),
            'color'         => $model->color,

            /* place your other model properties here */

            // 'created_at' => $model->created_at,
            // 'updated_at' => $model->updated_at
        ];
    }

    public function includeCompany(User $user)
    {
        $company = empty($user->company) ? new Company : $user->company;

        return $this->item($company, new CompanyTransformer);
    }

    /**
     * Customers
     *
     * @param User $model
     * @return void
     */
    public function customers(User $model)
    {
        $manager = new Manager();
        $manager->setSerializer(new DataArraySerializer());

        $userOwnCustomers = UserCustomer::where('user_id', $model->id)->pluck('customer_id')->all();

        if ($userOwnCustomers) {
            $customers = Customer::whereIn('id', $userOwnCustomers)->get();
            $resource = $this->collection($customers, new CustomerNoRecursiveTransformer);

            $collection = $manager->createData($resource)->toArray();

            return $collection['data'];

        }

        return NULL;
    }

    /**
     * Roles
     *
     * @param User $model
     * @return void
     */
    public function roles(User $model)
    {
        $manager = new Manager();
        $manager->setSerializer(new DataArraySerializer());

        $userHasRole = UserHasRole::where('user_id', $model->id)->pluck('role_id')->all();
        if ($userHasRole) {
            $roles = Role::whereIn('id', $userHasRole)->get();

            $resource = $this->collection($roles, new RoleTransformer);

            $collection = $manager->createData($resource)->toArray();

            return $collection['data'];

        }

        return NULL;
    }

    /**
     * Show Company List by user Customer
     *
     * @param User $model
     * @return void
     */
    public function companyList($model)
    {
        $manager = new Manager();
        $manager->setSerializer(new DataArraySerializer());

        $customerList = UserCustomer::where('user_id', $model->id)->pluck('customer_id')->all();

        if ($customerList) {
            $customers = Customer::whereIn('id', $customerList)->get();
            $resource = $this->collection($customers, new UserCustomerCompanyTransformer);

            $collection = $manager->createData($resource)->toArray();

            return $collection['data'];
        }

        return NULL;
    }
}
