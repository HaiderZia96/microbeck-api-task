<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin',
                'email' => 'admin@admin.com',
                'password'=>bcrypt('11112222#'),
                'email_verified_at'  => Carbon::now()->toDateTimeString(),
                'user_role' => 'A',
            ]
        ];
        foreach ($users as $user){
            $user = User::create($user);
            $user->assignRole(Role::all());
        }
    }
}
