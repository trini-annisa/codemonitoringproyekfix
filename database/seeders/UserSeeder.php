<?php
namespace Database\Seeders;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder {
    public function run(): void {
        User::create(['name'=>'Administrator','email'=>'admin@eva.test','password'=>Hash::make('password'),'role'=>'admin','is_active'=>true]);
        User::create(['name'=>'Budi Santoso','email'=>'budi@eva.test','password'=>Hash::make('password'),'role'=>'project_manager','is_active'=>true]);
        User::create(['name'=>'Siti Rahayu','email'=>'siti@eva.test','password'=>Hash::make('password'),'role'=>'project_manager','is_active'=>true]);
    }
}
