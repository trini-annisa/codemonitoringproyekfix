<?php
namespace Database\Seeders;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder {
    public function run(): void {
        $admin = User::where('role','admin')->first();
        $pm1   = User::where('email','budi@eva.test')->first();
        $pm2   = User::where('email','siti@eva.test')->first();

        Project::create(['project_code'=>'PRJ-2024-001','name'=>'Pembangunan Gedung Kantor DPU','description'=>'Pembangunan gedung 4 lantai','location'=>'Jl. Sudirman No. 45, Makassar','owner'=>'Dinas PU Kota Makassar','contract_value'=>4500000000,'bac'=>4350000000,'start_date'=>'2024-01-15','end_date'=>'2024-12-31','status'=>'active','contract_number'=>'KTR/DPU/2024/001','pm_id'=>$pm1->id,'created_by'=>$admin->id]);
        Project::create(['project_code'=>'PRJ-2024-002','name'=>'Renovasi Jembatan Tol Reformasi','description'=>'Penguatan struktur jembatan 120m','location'=>'Tol Reformasi KM 12','owner'=>'PT Jasa Marga Sulsel','contract_value'=>2800000000,'bac'=>2750000000,'start_date'=>'2024-03-01','end_date'=>'2024-09-30','status'=>'active','contract_number'=>'KTR/JM/2024/012','pm_id'=>$pm2->id,'created_by'=>$admin->id]);
        Project::create(['project_code'=>'PRJ-2023-005','name'=>'Perumahan Subsidi Tipe 36','description'=>'100 unit rumah subsidi','location'=>'Perumnas Antang, Makassar','owner'=>'PT Griya Nusantara','contract_value'=>8200000000,'bac'=>8050000000,'start_date'=>'2023-06-01','end_date'=>'2024-06-30','status'=>'completed','contract_number'=>'KTR/GN/2023/005','pm_id'=>$pm1->id,'created_by'=>$admin->id]);
    }
}
