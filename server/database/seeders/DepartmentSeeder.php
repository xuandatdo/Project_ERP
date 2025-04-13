<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\Position;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Phòng Hành chính - Nhân sự
        $hrDept = Department::create([
            'name' => 'Phòng Hành chính - Nhân sự',
            'description' => 'Quản lý nhân sự và hành chính công ty'
        ]);
        
        Position::create([
            'name' => 'Trưởng phòng',
            'description' => 'Trưởng phòng Hành chính - Nhân sự',
            'department_id' => $hrDept->id
        ]);
        
        Position::create([
            'name' => 'Nhân sự',
            'description' => 'Nhân viên nhân sự',
            'department_id' => $hrDept->id
        ]);

        // Phòng Kế toán
        $accountingDept = Department::create([
            'name' => 'Phòng Kế toán',
            'description' => 'Quản lý tài chính và kế toán công ty'
        ]);
        
        Position::create([
            'name' => 'Kế toán trưởng',
            'description' => 'Trưởng phòng kế toán',
            'department_id' => $accountingDept->id
        ]);
        
        Position::create([
            'name' => 'Kế toán công nợ',
            'description' => 'Nhân viên kế toán công nợ',
            'department_id' => $accountingDept->id
        ]);

        // Phòng Kinh doanh
        $salesDept = Department::create([
            'name' => 'Phòng Kinh doanh',
            'description' => 'Quản lý hoạt động kinh doanh và bán hàng'
        ]);
        
        Position::create([
            'name' => 'Giám đốc kinh doanh',
            'description' => 'Giám đốc phòng kinh doanh',
            'department_id' => $salesDept->id
        ]);
        
        Position::create([
            'name' => 'Nhân viên sales',
            'description' => 'Nhân viên bán hàng',
            'department_id' => $salesDept->id
        ]);

        // Phòng Công nghệ thông tin
        $itDept = Department::create([
            'name' => 'Phòng Công nghệ thông tin',
            'description' => 'Quản lý hệ thống CNTT và phát triển phần mềm'
        ]);
        
        Position::create([
            'name' => 'Lập trình viên',
            'description' => 'Nhân viên phát triển phần mềm',
            'department_id' => $itDept->id
        ]);
        
        Position::create([
            'name' => 'IT Support',
            'description' => 'Nhân viên hỗ trợ kỹ thuật',
            'department_id' => $itDept->id
        ]);
        
        Position::create([
            'name' => 'Quản trị hệ thống',
            'description' => 'Nhân viên quản trị hệ thống',
            'department_id' => $itDept->id
        ]);

        // Phòng Ban Giám đốc
        $boardDept = Department::create([
            'name' => 'Phòng Ban Giám đốc',
            'description' => 'Ban lãnh đạo công ty'
        ]);
        
        Position::create([
            'name' => 'CEO',
            'description' => 'Giám đốc điều hành',
            'department_id' => $boardDept->id
        ]);
        
        Position::create([
            'name' => 'Phó giám đốc',
            'description' => 'Phó giám đốc công ty',
            'department_id' => $boardDept->id
        ]);
        
        Position::create([
            'name' => 'Trợ lý giám đốc',
            'description' => 'Trợ lý cho ban giám đốc',
            'department_id' => $boardDept->id
        ]);

        // Phòng Marketing
        $marketingDept = Department::create([
            'name' => 'Phòng Marketing',
            'description' => 'Quản lý hoạt động marketing và truyền thông'
        ]);
        
        Position::create([
            'name' => 'Trưởng phòng',
            'description' => 'Trưởng phòng marketing',
            'department_id' => $marketingDept->id
        ]);
        
        Position::create([
            'name' => 'Digital Marketing',
            'description' => 'Nhân viên marketing số',
            'department_id' => $marketingDept->id
        ]);
        
        Position::create([
            'name' => 'Content',
            'description' => 'Nhân viên sáng tạo nội dung',
            'department_id' => $marketingDept->id
        ]);
    }
}