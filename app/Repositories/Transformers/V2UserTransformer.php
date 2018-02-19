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
 * Class V2UserTransformer
 * @package namespace App\Repositories\Transformers;
 */
class V2UserTransformer extends TransformerAbstract
{

    protected $defaultIncludes = [
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
        ];
    }
}
