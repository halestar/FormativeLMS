<?php

namespace Database\Seeders;

use App\Classes\Integrators\Local\LocalIntegrator;
use App\Enums\IntegratorServiceTypes;
use App\Enums\WorkStoragesInstances;
use App\Models\Integrations\IntegrationConnection;
use App\Models\People\Person;
use App\Models\Utilities\SystemSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class SystemSettingsSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		$classManagementService = LocalIntegrator::getService(IntegratorServiceTypes::CLASSES);
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
				],
				"max_msg" =>  "10",
				"year_msg" => 2,
				"rubrics_max_points" => "5",
				"force_class_management" => true,
				"class_management_service_id" => $classManagementService->id
			];
		SystemSetting::create
		(
			[
				'name' => 'school',
				'value' => $schoolSettings,
			]
		);
		
		$idStr = '{"global_id": {"rows": 4, "columns": 3, "content": [[{"config": {"bold": true, "color": "#f8dc25", "italics": false, "font-size": "3", "underline": false, "text-align": "center", "custom-text": "Kalinec School", "font-family": "Brush Script MT", "text-shadow": true, "text-shadow-x": 2, "text-shadow-y": 2, "text-shadow-blur": 2, "text-shadow-color": "#000000"}, "colspan": 3, "rowspan": 1, "className": "App\\\\Classes\\\\IdCard\\\\CustomText"}, 2, 2], [{"config": {"border": true, "img-margin": "5", "img-shadow": false, "img-padding": "5", "border-color": "#000000", "border-style": "solid", "border-width": 1, "img-shadow-x": 2, "img-shadow-y": 2, "border-radius": "25", "img-shadow-blur": 2, "img-shadow-color": "#000000"}, "colspan": 1, "rowspan": 2, "className": "App\\\\Classes\\\\IdCard\\\\PersonPicture"}, {"config": {"bold": true, "color": "#f8dc25", "italics": false, "font-size": "2", "underline": false, "text-align": "center", "font-family": "Arial", "text-shadow": true, "text-shadow-x": 2, "text-shadow-y": 2, "text-shadow-blur": 2, "text-shadow-color": "#000000"}, "colspan": 2, "rowspan": 1, "className": "App\\\\Classes\\\\IdCard\\\\PersonName"}, 2], [2, {"config": {"bold": false, "color": "#f2d726", "italics": true, "font-size": "1.5", "underline": false, "text-align": "center", "font-family": "Arial", "text-shadow": true, "text-shadow-x": 2, "text-shadow-y": 2, "text-shadow-blur": 2, "text-shadow-color": "#000000"}, "colspan": 2, "rowspan": 1, "className": "App\\\\Classes\\\\IdCard\\\\SchoolYear"}, 2], [{"config": {"bold": true, "color": "#000000", "italics": false, "font-size": "1", "underline": false, "text-align": "center", "font-family": "Arial", "text-shadow": false, "text-shadow-x": 2, "text-shadow-y": 2, "text-shadow-blur": 2, "text-shadow-color": "#000000"}, "colspan": 1, "rowspan": 1, "className": "App\\\\Classes\\\\IdCard\\\\SchoolId"}, {"config": {"barcode-type": "code-128", "barcode-padding": "3", "barcode-scale-x": "2", "barcode-scale-y": "0.3", "barcode-bg-color": "#ffffff", "barcode-bar-color": "#000000", "barcode-text-font": "monospace", "barcode-text-size": "8", "barcode-text-color": "#000000", "barcode-space-color": "#ffffff", "barcode-transparent-bg": false, "barcode-transparent-space": false}, "colspan": 2, "rowspan": 1, "className": "App\\\\Classes\\\\IdCard\\\\SchoolIdBarcode"}, 2]], "preview": "<div\\n    class=\\"card\\"\\n            style=\\"aspect-ratio: 1.59 !important;\\n                background-color: rgba(44, 56, 232, 0.58);\\n                                background-blend-mode: normal;\\n                padding: 10px;\\n                width: 400px !important;\\n                max-width: 400px;\\n                \\"\\n    >\\n            <table style=\\"width: 100%; height: 100%;table-layout: fixed;\\">\\n                            <tr style=\\"padding: 0;margin: 0;\\">\\n                                                                    <td\\n                            style=\\"position: relative;\\"\\n                                                            colspan=\\"3\\"\\n                            rowspan=\\"1\\"\\n                                                    >\\n                                                            <div class=\\"d-flex justify-content-center align-items-center\\">\\n                                    <div\\n                                        class=\\"flex-grow-1 show-as-action d-flex justify-content-start align-items-center\\"\\n                                    >\\n                                        <div style=\\"width: 100%;;font-size: 3em;font-weight: bold;color: #f8dc25;text-align: center;font-family: Brush Script MT,sans-serif;text-shadow: 2px 2px 2px #000000;\\">Kalinec School</div>\\n                                    </div>\\n                                </div>\\n                                                    </td>\\n                                                                                    </tr>\\n                            <tr style=\\"padding: 0;margin: 0;\\">\\n                                                                    <td\\n                            style=\\"position: relative;\\"\\n                                                            colspan=\\"1\\"\\n                            rowspan=\\"2\\"\\n                                                    >\\n                                                            <div class=\\"d-flex justify-content-center align-items-center\\">\\n                                    <div\\n                                        class=\\"flex-grow-1 show-as-action d-flex justify-content-start align-items-center\\"\\n                                    >\\n                                        <img class=\'img-fluid\' style=\'border-radius: 25%;border-width: 1px;border-style: solid;border-color: #000000;padding: 5px;margin: 5px;\' src=\'data:image/svg+xml,<svg xmlns=\\"http://www.w3.org/2000/svg\\" viewBox=\\"0 0 448 512\\"><path d=\\"M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512l388.6 0c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304l-91.4 0z\\"/></svg>\' />\\n                                    </div>\\n                                </div>\\n                                                    </td>\\n                                                                    <td\\n                            style=\\"position: relative;\\"\\n                                                            colspan=\\"2\\"\\n                            rowspan=\\"1\\"\\n                                                    >\\n                                                            <div class=\\"d-flex justify-content-center align-items-center\\">\\n                                    <div\\n                                        class=\\"flex-grow-1 show-as-action d-flex justify-content-start align-items-center\\"\\n                                    >\\n                                        <div style=\\"width: 100%;;font-size: 2em;font-weight: bold;color: #f8dc25;text-align: center;font-family: Arial,sans-serif;text-shadow: 2px 2px 2px #000000;\\">Person Name</div>\\n                                    </div>\\n                                </div>\\n                                                    </td>\\n                                                            </tr>\\n                            <tr style=\\"padding: 0;margin: 0;\\">\\n                                                                                            <td\\n                            style=\\"position: relative;\\"\\n                                                            colspan=\\"2\\"\\n                            rowspan=\\"1\\"\\n                                                    >\\n                                                            <div class=\\"d-flex justify-content-center align-items-center\\">\\n                                    <div\\n                                        class=\\"flex-grow-1 show-as-action d-flex justify-content-start align-items-center\\"\\n                                    >\\n                                        <div style=\\"width: 100%;;font-size: 1.5em;font-style: italic;color: #f2d726;text-align: center;font-family: Arial,sans-serif;text-shadow: 2px 2px 2px #000000;\\">2024 - 2025</div>\\n                                    </div>\\n                                </div>\\n                                                    </td>\\n                                                            </tr>\\n                            <tr style=\\"padding: 0;margin: 0;\\">\\n                                                                    <td\\n                            style=\\"position: relative;\\"\\n                                                            colspan=\\"1\\"\\n                            rowspan=\\"1\\"\\n                                                    >\\n                                                            <div class=\\"d-flex justify-content-center align-items-center\\">\\n                                    <div\\n                                        class=\\"flex-grow-1 show-as-action d-flex justify-content-start align-items-center\\"\\n                                    >\\n                                        <div style=\\"width: 100%;;font-size: 1em;font-weight: bold;color: #000000;text-align: center;font-family: Arial,sans-serif;\\">0000000000</div>\\n                                    </div>\\n                                </div>\\n                                                    </td>\\n                                                                    <td\\n                            style=\\"position: relative;\\"\\n                                                            colspan=\\"2\\"\\n                            rowspan=\\"1\\"\\n                                                    >\\n                                                            <div class=\\"d-flex justify-content-center align-items-center\\">\\n                                    <div\\n                                        class=\\"flex-grow-1 show-as-action d-flex justify-content-start align-items-center\\"\\n                                    >\\n                                        <div class=\'m-auto\'><?xml version=\\"1.0\\"?><svg xmlns=\\"http://www.w3.org/2000/svg\\" version=\\"1.1\\" width=\\"226\\" height=\\"30\\" viewBox=\\"0 0 226 30\\"><g><rect x=\\"0\\" y=\\"0\\" width=\\"226\\" height=\\"30\\" fill=\\"#ffffff\\"/><g transform=\\"translate(3 3) scale(2 1)\\"><g><rect x=\\"0\\" y=\\"0\\" width=\\"10\\" height=\\"24\\" fill=\\"#ffffff\\"/><rect x=\\"10\\" y=\\"0\\" width=\\"2\\" height=\\"24\\" fill=\\"#000000\\"/><rect x=\\"12\\" y=\\"0\\" width=\\"1\\" height=\\"24\\" fill=\\"#ffffff\\"/><rect x=\\"13\\" y=\\"0\\" width=\\"1\\" height=\\"24\\" fill=\\"#000000\\"/><rect x=\\"14\\" y=\\"0\\" width=\\"2\\" height=\\"24\\" fill=\\"#ffffff\\"/><rect x=\\"16\\" y=\\"0\\" width=\\"3\\" height=\\"24\\" fill=\\"#000000\\"/><rect x=\\"19\\" y=\\"0\\" width=\\"2\\" height=\\"24\\" fill=\\"#ffffff\\"/><rect x=\\"21\\" y=\\"0\\" width=\\"2\\" height=\\"24\\" fill=\\"#000000\\"/><rect x=\\"23\\" y=\\"0\\" width=\\"1\\" height=\\"24\\" fill=\\"#ffffff\\"/><rect x=\\"24\\" y=\\"0\\" width=\\"2\\" height=\\"24\\" fill=\\"#000000\\"/><rect x=\\"26\\" y=\\"0\\" width=\\"2\\" height=\\"24\\" fill=\\"#ffffff\\"/><rect x=\\"28\\" y=\\"0\\" width=\\"2\\" height=\\"24\\" fill=\\"#000000\\"/><rect x=\\"30\\" y=\\"0\\" width=\\"2\\" height=\\"24\\" fill=\\"#ffffff\\"/><rect x=\\"32\\" y=\\"0\\" width=\\"2\\" height=\\"24\\" fill=\\"#000000\\"/><rect x=\\"34\\" y=\\"0\\" width=\\"1\\" height=\\"24\\" fill=\\"#ffffff\\"/><rect x=\\"35\\" y=\\"0\\" width=\\"2\\" height=\\"24\\" fill=\\"#000000\\"/><rect x=\\"37\\" y=\\"0\\" width=\\"2\\" height=\\"24\\" fill=\\"#ffffff\\"/><rect x=\\"39\\" y=\\"0\\" width=\\"2\\" height=\\"24\\" fill=\\"#000000\\"/><rect x=\\"41\\" y=\\"0\\" width=\\"2\\" height=\\"24\\" fill=\\"#ffffff\\"/><rect x=\\"43\\" y=\\"0\\" width=\\"2\\" height=\\"24\\" fill=\\"#000000\\"/><rect x=\\"45\\" y=\\"0\\" width=\\"1\\" height=\\"24\\" fill=\\"#ffffff\\"/><rect x=\\"46\\" y=\\"0\\" width=\\"2\\" height=\\"24\\" fill=\\"#000000\\"/><rect x=\\"48\\" y=\\"0\\" width=\\"2\\" height=\\"24\\" fill=\\"#ffffff\\"/><rect x=\\"50\\" y=\\"0\\" width=\\"2\\" height=\\"24\\" fill=\\"#000000\\"/><rect x=\\"52\\" y=\\"0\\" width=\\"2\\" height=\\"24\\" fill=\\"#ffffff\\"/><rect x=\\"54\\" y=\\"0\\" width=\\"2\\" height=\\"24\\" fill=\\"#000000\\"/><rect x=\\"56\\" y=\\"0\\" width=\\"1\\" height=\\"24\\" fill=\\"#ffffff\\"/><rect x=\\"57\\" y=\\"0\\" width=\\"2\\" height=\\"24\\" fill=\\"#000000\\"/><rect x=\\"59\\" y=\\"0\\" width=\\"2\\" height=\\"24\\" fill=\\"#ffffff\\"/><rect x=\\"61\\" y=\\"0\\" width=\\"2\\" height=\\"24\\" fill=\\"#000000\\"/><rect x=\\"63\\" y=\\"0\\" width=\\"2\\" height=\\"24\\" fill=\\"#ffffff\\"/><rect x=\\"65\\" y=\\"0\\" width=\\"2\\" height=\\"24\\" fill=\\"#000000\\"/><rect x=\\"67\\" y=\\"0\\" width=\\"1\\" height=\\"24\\" fill=\\"#ffffff\\"/><rect x=\\"68\\" y=\\"0\\" width=\\"2\\" height=\\"24\\" fill=\\"#000000\\"/><rect x=\\"70\\" y=\\"0\\" width=\\"2\\" height=\\"24\\" fill=\\"#ffffff\\"/><rect x=\\"72\\" y=\\"0\\" width=\\"2\\" height=\\"24\\" fill=\\"#000000\\"/><rect x=\\"74\\" y=\\"0\\" width=\\"2\\" height=\\"24\\" fill=\\"#ffffff\\"/><rect x=\\"76\\" y=\\"0\\" width=\\"2\\" height=\\"24\\" fill=\\"#000000\\"/><rect x=\\"78\\" y=\\"0\\" width=\\"2\\" height=\\"24\\" fill=\\"#ffffff\\"/><rect x=\\"80\\" y=\\"0\\" width=\\"2\\" height=\\"24\\" fill=\\"#000000\\"/><rect x=\\"82\\" y=\\"0\\" width=\\"2\\" height=\\"24\\" fill=\\"#ffffff\\"/><rect x=\\"84\\" y=\\"0\\" width=\\"2\\" height=\\"24\\" fill=\\"#000000\\"/><rect x=\\"86\\" y=\\"0\\" width=\\"1\\" height=\\"24\\" fill=\\"#ffffff\\"/><rect x=\\"87\\" y=\\"0\\" width=\\"2\\" height=\\"24\\" fill=\\"#000000\\"/><rect x=\\"89\\" y=\\"0\\" width=\\"3\\" height=\\"24\\" fill=\\"#ffffff\\"/><rect x=\\"92\\" y=\\"0\\" width=\\"3\\" height=\\"24\\" fill=\\"#000000\\"/><rect x=\\"95\\" y=\\"0\\" width=\\"1\\" height=\\"24\\" fill=\\"#ffffff\\"/><rect x=\\"96\\" y=\\"0\\" width=\\"1\\" height=\\"24\\" fill=\\"#000000\\"/><rect x=\\"97\\" y=\\"0\\" width=\\"1\\" height=\\"24\\" fill=\\"#ffffff\\"/><rect x=\\"98\\" y=\\"0\\" width=\\"2\\" height=\\"24\\" fill=\\"#000000\\"/><rect x=\\"100\\" y=\\"0\\" width=\\"10\\" height=\\"24\\" fill=\\"#ffffff\\"/><text x=\\"55\\" y=\\"34\\" text-anchor=\\"middle\\" font-family=\\"monospace\\" font-size=\\"8\\" fill=\\"#000000\\">0000000000</text></g></g></g></svg></div>\\n                                    </div>\\n                                </div>\\n                                                    </td>\\n                                                            </tr>\\n                    </table>\\n    </div>\\n", "aspectRatio": 1.59, "contentPadding": 10, "backgroundColor": "#2c38e8", "backgroundImage": null, "backgroundBlendMode": "normal", "backgroundImageOpacity": 0.58}}';
		SystemSetting::create
		(
			[
				'name' => 'school-id-settings',
				'value' => json_decode($idStr, true),
			]
		);
		
		$work = [];
		//get the local work connection
		$localService = LocalIntegrator::getService(IntegratorServiceTypes::WORK);
        $localConnection = $localService->connectToSystem();
		foreach(WorkStoragesInstances::cases() as $workStorage)
			$work[$workStorage->value] = $localConnection?->id;
		$storage_settings =
			[
				'work' => $work
			];
		SystemSetting::create
		(
			[
				'name' => 'storage',
				'value' => $storage_settings,
			]
		);

		$commService = LocalIntegrator::getService(IntegratorServiceTypes::EMAIL);
		$commConnection = $commService->connectToSystem();
		$communicationsSettings =
		[
			'send_sms' => false,
			'email_from' => "FabLMS",
			'sms_connection_id' => null,
			'email_from_address' => "fablms@kalinec.net",
			'email_connection_id' => $commConnection->id,
		];
		SystemSetting::create
		(
			[
				'name' => 'communications',
				'value' => $communicationsSettings,
			]
		);
		$authService = LocalIntegrator::getService(IntegratorServiceTypes::AUTHENTICATION);

		$authSettings =
		[
			"upper" => true,
			"numbers" => true,
			"symbols" => true,
			"priorities" =>
			[
				[
					"roles" => [],
					"priority" => 0,
					"service_ids" => $authService->id,
				],
			],
			"min_password_length" => "8",
		];
		SystemSetting::create
		(
			[
				'name' => 'auth',
				'value' => $authSettings,
			]
		);
	}
}
