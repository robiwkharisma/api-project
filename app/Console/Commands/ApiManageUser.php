<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\Entities\User;
use Tymon\JWTAuth\JWTAuth;

class ApiManageUser extends Command
{
    public $authService;
    public $user;
    public $JWTAuth;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:manage-user
                                {userId}
                                {--password=}
                                {--getToken=}
                                ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'User Management';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(User $user, JWTAuth $JWTAuth)
    {
        parent::__construct();

        $this->user = $user;
        $this->JWTAuth = $JWTAuth;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $user = User::findOrFail($this->argument('userId'));

        if (!empty($this->option('password')))
        {
            $newPassword = $user->setPassword($this->option('password'));
            $user->save();

            $this->info("User #{$user->id} password changed! Password: {$newPassword}");
        }

        if (!empty($this->option('getToken')) && $this->option('getToken') == '1')
        {
            $token = $this->JWTAuth->fromUser($user);
            $this->info("User #{$user->id} token: {$token}");
        }
    }
}
