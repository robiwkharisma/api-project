<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserForAdminTest extends TestCase
{
    private $itemNumber = 0;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testUserList()
    {
        $response = $this->json(
            'GET',
            '/api/v1/users',
            [],
            [
                'Authorization' => 'Bearer '.$this->getToken('admin')
            ]);

        // HTTP 200
        $response->assertStatus(200);

        // Body Check
        $json = $response->json();

        // Code Check
        $this->assertTrue($json['code'] === 0);
        $this->itemNumber = count($json['data']);
    }
}
