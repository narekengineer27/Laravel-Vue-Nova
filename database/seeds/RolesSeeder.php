<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Schema;


class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!Schema::hasColumn('roles', 'admin') && Role::where('name', 'admin')->exists() == false) {
            Role::create(['name' => 'admin']);
            Role::create(['name' => 'consumer']);
        }
    }
}
