<?php

namespace App\Database\Seeds;

use App\Models\DepartmentModel;
use App\Models\ServiceModel;
use App\Models\UserModel;
use CodeIgniter\Database\Seeder;

/**
 * Data taken from the Sun Son Solar interviews:
 *  - the 8 departments
 *  - the first two registered users (Kat and Sol, both admins)
 *  - the 7 services the company offers (prices are 0 until the admin sets them in Settings)
 *
 * Run:  php spark db:seed InitialDataSeeder
 * Safe to run twice (skips what already exists).
 */
class InitialDataSeeder extends Seeder
{
    public function run()
    {
        // ---- departments ----
        $departments = new DepartmentModel();
        foreach (['Administration', 'IT', 'Dispatch', 'Accounting', 'HR', 'Marketing', 'Sales', 'Customer Service'] as $name) {
            if (! $departments->where('name', $name)->first()) {
                $departments->insert(['name' => $name]);
            }
        }
        $dept = fn (string $n) => $departments->where('name', $n)->first()['id'];

        // ---- first users ----
        $users = new UserModel();
        $seed  = [
            [   // Founder
                'first_name'  => 'Katherine',
                'middle_name' => 'Olap',
                'last_name'   => 'Sinagaraw',
                'birthdate'   => '1990-07-01',
                'gender'      => null, // not stated in the interview
                'email'       => 'katherine.sinagaraw@sunsonsolar.com',
                'phone'       => '09291230983', // company number
                'address'     => null,
                'username'    => 'KittyKat16',
                'password'    => 'K@tSunShine16',
                'role'        => 'admin',
                'user_type'   => 'employee',
                'department'  => 'Administration',
            ],
            [   // IT head
                'first_name'  => 'Sol',
                'middle_name' => 'Sun',
                'last_name'   => 'Solis',
                'birthdate'   => '1967-01-08',
                'gender'      => null, // not stated in the interview
                'email'       => 'sol.solis@sunsonsolar.com', // interview was cut off at "sol.solis," -> confirm domain
                'phone'       => '09291230983', // "same thing" as Kat (company number)
                'address'     => null,
                'username'    => 'admin',
                'password'    => 'admin123', // Sol will change this after handover
                'role'        => 'admin',
                'user_type'   => 'employee',
                'department'  => 'IT',
            ],
        ];
        foreach ($seed as $u) {
            if ($users->where('username', $u['username'])->first()) {
                continue;
            }
            $u['department_id'] = $dept($u['department']);
            unset($u['department']);
            $users->insert($u);
        }

        // ---- services ----
        $services = new ServiceModel();
        $list = [
            'Consultation' => 'Talk to our experts about the right solar setup for your home or business.',
            'Designing'    => 'Custom system design based on your roof, usage and budget.',
            'Permitting'   => 'We handle the permits and paperwork for your installation.',
            'Installation' => 'Professional installation of panels, inverters, batteries and mounting.',
            'Maintenance'  => 'Regular cleaning and inspection to keep your system at peak performance.',
            'Repair'       => 'Fast diagnosis and repair of solar system issues.',
            'Monitoring'   => 'Keep track of your system output and catch problems early.',
        ];
        foreach ($list as $name => $desc) {
            if (! $services->where('name', $name)->first()) {
                $services->insert(['name' => $name, 'description' => $desc, 'price' => 0, 'duration_minutes' => 60, 'is_active' => 1]);
            }
        }
    }
}
