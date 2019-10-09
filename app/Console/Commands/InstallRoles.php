<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class InstallRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'roles:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install roles and permissions';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Reset necessary tables');
        DB::table( 'model_has_permissions' )->delete();
        DB::table( 'model_has_roles' )->delete();
        DB::table( 'role_has_permissions' )->delete();
        DB::table( 'permissions' )->delete();
        DB::table( 'roles' )->delete();
        app()['cache']->forget('spatie.permission.cache');

        $this->info( 'Installing roles' );
        Role::create([ 'name' => 'user', 'guard_name' => 'api' ]);
        Role::create([ 'name' => 'admin', 'guard_name' => 'api']);
        Role::create([ 'name' => 'super', 'guard_name' => 'api']);
        Role::create([ 'name' => 'teacher', 'guard_name' => 'api' ]);
        Role::create([ 'name' => 'student', 'guard_name' => 'api' ]);
    }
}
