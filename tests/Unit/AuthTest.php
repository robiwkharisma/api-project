<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AuthTest extends TestCase
{
    /**
     * Login As Admin
     *
     * @return void
     */
    public function testLoginAsAdmin()
    {
        $response = $this->json(
            'POST',
            '/api/v1/auth/login',
            [
                'email' => 'admin@super.com',
                'password' => 'password',
            ]);

        // HTTP 200
        $response->assertStatus(200);

        // Body Check
        $json = $response->json();

        // Code Check
        $this->assertTrue($json['code'] === 0);

        // Token Check
        $this->data_token = $json['data']['token'];
        $this->assertJwtToken($this->data_token);

        // userType: admin
        $this->assertTrue($json['data']['userType'] === 'admin');
    }
}
