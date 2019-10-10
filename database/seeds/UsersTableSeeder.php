<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table( 'users' )->truncate();
        DB::table( 'model_has_roles' )->truncate();
        DB::table( 'media' )->where( 'model_type', 'App\User' )->delete();

        collect( Storage::disk( 'public' )->allDirectories() )->each( function ( $directory )
        {
            Storage::disk( 'public' )->deleteDirectory( $directory );
        } );

        factory( \App\User::class, 100 )->create()->each( function ( $user ) {
            $roleLottery = rand( 0, 20 );

            if ( $roleLottery == 0 )
                $user->assignRole([ 'super' ]);
            elseif ( $roleLottery < 2 )
                $user->assignRole([ 'admin' ]);
            else
                $user->assignRole([ 'user' ]);

            $avatarPath = Storage::disk('public')->path('').'/'.'avatar-'.$user->id.'.png';
            \Avatar::create($user->name)->save($avatarPath,100);
            $user->addMedia($avatarPath )->toMediaCollection( 'avatar','public' );
        } );
    }
}
