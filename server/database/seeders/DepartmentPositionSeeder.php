<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\Position;

class DepartmentPositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạo các phòng ban mẫu
        $departments = [
            ['name' => 'Hành chính-Nhân sự', 'description' => 'Phòng quản lý hành chính và nhân sự'],
            ['name' => 'Kế toán', 'description' => 'Phòng quản lý tài chính và kế toán'],
            ['name' => 'Kinh doanh', 'description' => 'Phòng phát triển kinh doanh và bán hàng'],
            ['name' => 'CNTT', 'description' => 'Phòng công nghệ thông tin'],
            ['name' => 'Ban Giám đốc', 'description' => 'Ban lãnh đạo công ty'],
            ['name' => 'Marketing', 'description' => 'Phòng tiếp thị và quảng cáo'],
        ];

        foreach ($departments as $deptData) {
            $department = Department::create($deptData);

            // Tạo các vị trí tương ứng cho từng phòng ban
            switch ($department->name) {
                case 'Hành chính-Nhân sự':
                    $positions = [
                        ['name' => 'Trưởng phòng nhân sự', 'description' => 'Quản lý chung phòng nhân sự'],
                        ['name' => 'Chuyên viên tuyển dụng', 'description' => 'Phụ trách tuyển dụng nhân sự mới'],
                        ['name' => 'Chuyên viên đào tạo', 'description' => 'Phụ trách đào tạo và phát triển nhân viên'],
                    ];
                    break;
                case 'Kế toán':
                    $positions = [
                        ['name' => 'Kế toán trưởng', 'description' => 'Quản lý chung phòng kế toán'],
                        ['name' => 'Kế toán thanh toán', 'description' => 'Phụ trách thanh toán và công nợ'],
                        ['name' => 'Kế toán thuế', 'description' => 'Phụ trách các vấn đề về thuế'],
                    ];
                    break;
                case 'CNTT':
                    $positions = [
                        ['name' => 'Giám đốc CNTT', 'description' => 'Quản lý chung phòng IT'],
                        ['name' => 'Lập trình viên', 'description' => 'Phát triển phần mềm và ứng dụng'],
                        ['name' => 'Kỹ sư hệ thống', 'description' => 'Quản lý hạ tầng và hệ thống CNTT'],
                        ['name' => 'Chuyên viên hỗ trợ kỹ thuật', 'description' => 'Hỗ trợ người dùng và xử lý sự cố'],
                    ];
                    break;
                case 'Ban Giám đốc':
                    $positions = [
                        ['name' => 'Giám đốc điều hành', 'description' => 'Điều hành chung toàn bộ công ty'],
                        ['name' => 'Phó giám đốc', 'description' => 'Hỗ trợ giám đốc điều hành công ty'],
                        ['name' => 'Thư ký giám đốc', 'description' => 'Hỗ trợ công việc hành chính cho ban giám đốc'],
                    ];
                    break;
                case 'Marketing':
                    $positions = [
                        ['name' => 'Giám đốc Marketing', 'description' => 'Quản lý chung phòng Marketing'],
                        ['name' => 'Chuyên viên truyền thông', 'description' => 'Phụ trách truyền thông và PR'],
                        ['name' => 'Chuyên viên digital marketing', 'description' => 'Phụ trách marketing trên các kênh số'],
                    ];
                    break;
                case 'Kinh doanh':
                    $positions = [
                        ['name' => 'Giám đốc kinh doanh', 'description' => 'Quản lý chung phòng kinh doanh'],
                        ['name' => 'Nhân viên kinh doanh', 'description' => 'Phụ trách bán hàng và phát triển khách hàng'],
                        ['name' => 'Chuyên viên chăm sóc khách hàng', 'description' => 'Phụ trách chăm sóc và duy trì khách hàng'],
                    ];
                    break;
                default:
                    $positions = [];
            }

            // Thêm vị trí vào cơ sở dữ liệu
            foreach ($positions as $posData) {
                Position::create([
                    'name' => $posData['name'],
                    'description' => $posData['description'],
                    'department_id' => $department->id,
                ]);
            }
        }
    }
}
