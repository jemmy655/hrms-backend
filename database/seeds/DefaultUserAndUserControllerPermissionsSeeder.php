<?php

use App\Permission;
use App\Role;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;

class DefaultUserAndUserControllerPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**
         * Create default user
         */
        $user = new User();
        $user->id = 1;
        $user->name = "Admin User";
        $user->email = "admin@" . parse_url(Config::get('app.url'), PHP_URL_HOST);
        $user->password = bcrypt('password');
        $user->save();

        /**
         * Create default role (Admin Role)
         */
        $admin = new Role();
        $admin->name         = 'admin';
        $admin->display_name = 'Administrator';
        $admin->description  = 'Can access all part of the system';
        $admin->save();

        /**
         * Attach defaut user to default role
         */
        $user->attachRole($admin);

        /**
         * Create usersController permissions
         */
        $createUsers = new Permission();
        $createUsers->name =  "create-users";
        $createUsers->display_name = "Create users";
        $createUsers->description  = "Ability to create Users";
        $createUsers->save();

        $viewUser = new Permission();
        $viewUser->name = "view-user";
        $viewUser->display_name = "View User";
        $viewUser->description  = "Ability to view any user details";
        $viewUser->save();

        $listUsers = new Permission();
        $listUsers->name = "list-users";
        $listUsers->display_name = "List Users";
        $listUsers->description  = "Ability to list all users";
        $listUsers->save();

        $updateUsers = new Permission();
        $updateUsers->name = "update-users";
        $updateUsers->display_name = "Update Users";
        $updateUsers->description  = "Ability to update any user details";
        $updateUsers->save();

        /**
         * Attach usersController permissions to default user
         */
        $admin->attachPermissions(array($createUsers, $viewUser, $listUsers, $updateUsers));

    }
}
