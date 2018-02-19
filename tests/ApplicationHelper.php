<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;

trait ApplicationHelper
{
    public $user_role = 'customer';
    public $data_token;
    public $data_token_admin;
    public $data_token_customer;

    private $userCredentialAdmin = [
        'email'    => 'admin@super.com',
        'password' => 'password'
    ];
    private $userCredentialCustomer = [
        'email'    => 'admin@customer.com',
        'password' => 'password'
    ];

    /**
     * Assert JWT TOken
     *
     * @param string $token
     * @return boolean
     */
    public function assertJwtToken($token)
    {
        return $this->assertTrue(!empty($token));
    }

    public function assertModelProperty($jsonProperty)
    {

    }

    /**
     * Login and get data token by role
     *
     * @param string $userRole
     * @return string
     */
    public function getUserToken($userRole)
    {
        // Get credential for the user role
        switch ($userRole) {
            case 'admin':
                $credential = $this->userCredentialAdmin;
                $this->data_token = $this->data_token_admin;
                break;
            case 'customer':
            default:
                $credential = $this->userCredentialCustomer;
                $this->data_token = $this->data_token_customer;
                break;
        }

        // Check current data_token
        if (!empty($this->data_token))
            return $this->data_token;

        $response = $this->json(
            'POST',
            '/api/v1/auth/login',
            $credential);

        // Body Check
        $json = $response->json();

        // Token Check
        $this->data_token_customer = $json['data']['token'];

        return $this->data_token_customer;
    }

    /**
     * Get User Token
     *
     * @param string $userRole
     * @return string
     */
    public function getToken($userRole = 'customer')
    {
        $this->data_token = $this->getUserToken($userRole);

        return $this->data_token;
    }
}
