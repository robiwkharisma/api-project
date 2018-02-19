<?php

namespace App\Repositories\Auth\Contract;

use Tymon\JWTAuth\Providers\Auth\AuthInterface;

/**
 * Interface AuthRepository
 * @package namespace App\Repositories\Contract;
 */
interface AuthRepository extends AuthInterface
{
    public function getLoginData($token);

    public function resetPassword($param);

    public function changePassword($param);
}
