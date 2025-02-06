<?php

namespace Database\Seeders;

use App\Models\Utilities\SystemSetting;
use Illuminate\Database\Seeder;

class SystemSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $schoolSettings =
            [
                "parentName" =>
                    [
                        [
                            "type" => 1,
                            "roleId" => null,
                            "roleField" => null,
                            "spaceAfter" => true,
                            "textContent" => null,
                            "basicFieldName" => "first",
                        ],
                        [
                            "type" => 1,
                            "roleId" => null,
                            "roleField" => null,
                            "spaceAfter" => false,
                            "textContent" => null,
                            "basicFieldName" => "last",
                        ]
                    ],
                "studentName" =>
                    [
                        [
                            "type" => 1,
                            "roleId" => null,
                            "roleField" => null,
                            "spaceAfter" => true,
                            "textContent" => null,
                            "basicFieldName" => "preferred_first",
                        ],
                        [
                            "type" => 1,
                            "roleId" => null,
                            "roleField" => null,
                            "spaceAfter" => false,
                            "textContent" => null,
                            "basicFieldName" => "last",]
                    ],
                "employeeName" =>
                    [
                        [
                            "type" => 3,
                            "roleId" => null,
                            "roleField" => null,
                            "spaceAfter" => true,
                            "textContent" => "Mx.",
                            "basicFieldName" => null,
                        ],
                        [
                            "type" => 1,
                            "roleId" => null,
                            "roleField" => null,
                            "spaceAfter" => false,
                            "textContent" => null,
                            "basicFieldName" => "last",]
                    ]
            ];
        SystemSetting::create
        (
            [
                'name' => 'school',
                'value' => $schoolSettings,
            ]
        );
    }
}
