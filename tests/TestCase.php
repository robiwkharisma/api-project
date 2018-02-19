<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use ApplicationHelper;

    /**
     * Setup Test Case
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        // Rollback and refresh database with default seeder
        Artisan::call('migrate', [
            '--seed' => true,
        ]);

        // Prepare User Token
        $this->data_token_admin    = $this->getToken('admin');
        $this->data_token_customer = $this->getToken('customer');
    }

    public function tearDown()
    {
        // reset Database migration
        // Artisan::call('migrate:reset');

        parent::tearDown();
    }

}
