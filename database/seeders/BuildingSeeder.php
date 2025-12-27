<?php

namespace Database\Seeders;

use App\Models\Locations\Building;
use App\Models\Locations\BuildingArea;
use App\Models\Locations\Room;
use App\Models\People\Address;
use App\Models\People\Phone;
use App\Models\SystemTables\SchoolArea;
use Illuminate\Database\Seeder;

class BuildingSeeder extends Seeder
{
	public array $blueprints =
		[
			[
				'blueprint_url' => '/images/floorplans/a-floor-1.png',
				'rooms' =>
					[
						'[{"x":34,"y":21},{"x":119,"y":19},{"x":119,"y":187},{"x":37,"y":189},{"x":32,"y":22}]',
						'[{"x":36,"y":188},{"x":121,"y":188},{"x":120,"y":366},{"x":38,"y":368},{"x":37,"y":190}]',
						'[{"x":38,"y":408},{"x":119,"y":410},{"x":119,"y":565},{"x":35,"y":566},{"x":38,"y":407}]',
						'Women\'s Bathroom' => '[{"x":121,"y":408},{"x":156,"y":406},{"x":155,"y":518},{"x":117,"y":517},{"x":121,"y":408}]',
						'Nurse\'s Office' => '[{"x":159,"y":408},{"x":204,"y":407},{"x":205,"y":514},{"x":157,"y":516},{"x":158,"y":407}]',
						'Principal\'s Office' => '[{"x":205,"y":410},{"x":262,"y":408},{"x":262,"y":517},{"x":206,"y":517},{"x":204,"y":409}]',
						'[{"x":300,"y":409},{"x":406,"y":411},{"x":409,"y":517},{"x":296,"y":518},{"x":298,"y":410}]',
						'Men\'s Bathroom' => '[{"x":408,"y":407},{"x":455,"y":410},{"x":457,"y":515},{"x":412,"y":515},{"x":409,"y":410}]',
						'[{"x":457,"y":408},{"x":550,"y":411},{"x":553,"y":566},{"x":455,"y":563},{"x":458,"y":409}]',
						'[{"x":454,"y":190},{"x":551,"y":192},{"x":550,"y":369},{"x":457,"y":368},{"x":451,"y":191}]',
						'Kindergarten' => '[{"x":458,"y":188},{"x":553,"y":190},{"x":553,"y":162},{"x":582,"y":141},{"x":595,"y":101},{"x":580,"y":59},{"x":547,"y":37},{"x":550,"y":22},{"x":454,"y":21},{"x":458,"y":188}]',
						'Gym' => '[{"x":160,"y":30},{"x":425,"y":30},{"x":428,"y":255},{"x":156,"y":252},{"x":161,"y":32}]',
						'Gym Bleachers' => '[{"x":165,"y":277},{"x":414,"y":275},{"x":416,"y":353},{"x":158,"y":356},{"x":161,"y":278}]',
					]
			],
			[
				'blueprint_url' => '/images/floorplans/a-floor-2.png',
				'rooms' =>
					[
						'[{"x":38,"y":20},{"x":120,"y":21},{"x":121,"y":186},{"x":38,"y":186},{"x":39,"y":24}]',
						'Library' => '[{"x":41,"y":188},{"x":122,"y":188},{"x":120,"y":366},{"x":39,"y":368},{"x":39,"y":191}]',
						'[{"x":38,"y":410},{"x":123,"y":407},{"x":119,"y":568},{"x":36,"y":563},{"x":42,"y":411}]',
						'WB' => '[{"x":123,"y":410},{"x":158,"y":412},{"x":155,"y":517},{"x":123,"y":516},{"x":122,"y":408}]',
						'Lunchroom' => '[{"x":157,"y":409},{"x":371,"y":411},{"x":371,"y":518},{"x":159,"y":517},{"x":157,"y":407}]',
						'Kitchen' => '[{"x":368,"y":412},{"x":406,"y":410},{"x":407,"y":517},{"x":369,"y":517},{"x":368,"y":417}]',
						'MB' => '[{"x":407,"y":409},{"x":448,"y":411},{"x":453,"y":515},{"x":407,"y":517},{"x":404,"y":405}]',
						'[{"x":454,"y":409},{"x":554,"y":414},{"x":551,"y":565},{"x":454,"y":564},{"x":455,"y":406}]',
						'[{"x":453,"y":187},{"x":549,"y":185},{"x":551,"y":370},{"x":457,"y":368},{"x":451,"y":190}]',
						'Teacher Room A' => '[{"x":454,"y":120},{"x":547,"y":122},{"x":550,"y":189},{"x":456,"y":185},{"x":451,"y":117}]',
						'Teacher Room B' => '[{"x":464,"y":22},{"x":546,"y":20},{"x":550,"y":119},{"x":464,"y":121},{"x":460,"y":20}]',
						'Offices' => '[{"x":315,"y":257},{"x":427,"y":260},{"x":426,"y":371},{"x":306,"y":371},{"x":313,"y":256}]',
						'A/V Room' => '[{"x":201,"y":259},{"x":311,"y":258},{"x":309,"y":367},{"x":202,"y":367},{"x":202,"y":262}]',
						'Music Room' => '[{"x":147,"y":257},{"x":197,"y":258},{"x":197,"y":368},{"x":146,"y":370},{"x":147,"y":256}]',
					]
			],
			[
				'blueprint_url' => '/images/floorplans/b-floor-1.png',
				'rooms' =>
					[
						'101' => '[{"x":366,"y":426},{"x":401,"y":428},{"x":404,"y":470},{"x":368,"y":469},{"x":366,"y":423}]',
						'102' => '[{"x":403,"y":428},{"x":440,"y":431},{"x":442,"y":473},{"x":403,"y":470},{"x":402,"y":428}]',
						'103' => '[{"x":441,"y":427},{"x":475,"y":426},{"x":474,"y":472},{"x":440,"y":469},{"x":437,"y":424}]',
						'104' => '[{"x":481,"y":434},{"x":532,"y":434},{"x":535,"y":484},{"x":476,"y":482},{"x":477,"y":433}]',
					]
			],
			[
				'blueprint_url' => '/images/floorplans/b-floor-2.png',
				'rooms' =>
					[
						'201' => '[{"x":470,"y":399},{"x":519,"y":396},{"x":519,"y":446},{"x":467,"y":447},{"x":471,"y":396}]',
						'202' => '[{"x":515,"y":381},{"x":518,"y":458},{"x":578,"y":453},{"x":576,"y":379},{"x":508,"y":384},{"x":509,"y":386},{"x":516,"y":381}]',
						'203' => '[{"x":507,"y":340},{"x":554,"y":339},{"x":557,"y":379},{"x":504,"y":380},{"x":504,"y":341}]',
						'204' => '[{"x":502,"y":294},{"x":556,"y":291},{"x":553,"y":335},{"x":506,"y":335},{"x":503,"y":293}]',
					]
			],
			[
				'blueprint_url' => '/images/floorplans/b-floor-3.png',
				'rooms' =>
					[
						'301' => '[{"x":476,"y":389},{"x":530,"y":389},{"x":530,"y":433},{"x":480,"y":437},{"x":473,"y":389}]',
						'302' => '[{"x":495,"y":367},{"x":524,"y":369},{"x":523,"y":388},{"x":495,"y":388},{"x":491,"y":367}]',
						'303' => '[{"x":469,"y":322},{"x":514,"y":327},{"x":512,"y":368},{"x":468,"y":370},{"x":467,"y":321}]',
						'304' => '[{"x":468,"y":280},{"x":513,"y":279},{"x":514,"y":324},{"x":469,"y":327},{"x":464,"y":282}]',
					]
			],
		];
	
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		// here we create the campus for the school.
		// to make it easier, we will have 1 building with 4 areas and 16 rooms in the
		// buildings (which will also be tied randomly to campuses.)
		$mainAddress = Address::factory()
		                      ->create();
		$mainPhone = Phone::factory()
		                  ->create();
		
		$kalinecBuilding = Building::create(
			[
				'name' => 'The Kalinec Building',
				'img' => 'https://cdn.pixabay.com/photo/2021/10/11/04/08/university-6699377_1280.jpg',
				'address_id' => $mainAddress->id,
			]);
		$kalinecBuilding->phones()
		                ->attach($mainPhone->id, ['primary' => true]);

		$areaTable = SchoolArea::inRandomOrder()->take(5)->get()->pluck('id');
		for($i = 0; $i < 2; $i++)
		{
			$area = BuildingArea::create(
				[
					'building_id' => $kalinecBuilding->id,
					'area_id' => $areaTable[$i],
					'blueprint_url' => $this->blueprints[$i]['blueprint_url'],
					'order' => ($i + 1),
				]);
			foreach($this->blueprints[$i]['rooms'] as $roomName => $room)
			{
				Room::create(
					[
						'name' => is_numeric($roomName) ? fake()->numberBetween(($i + 1) * 100,
							($i + 1) * 100 + 99) : $roomName,
						'capacity' => fake()->numberBetween(10, 100),
						'img_data' => json_decode($room),
						'phone_id' => Phone::factory()
						                   ->extensionOnly()
						                   ->create()->id,
						'area_id' => $area->id,
					]);
			}
		}
		
		$mainAddress = Address::factory()
		                      ->create();
		$mainPhone = Phone::factory()
		                  ->create();
		$secondCampus = Building::create(
			[
				'name' => 'The Second Campus',
				'img' => 'https://cdn.pixabay.com/photo/2013/01/20/04/53/college-75535_640.jpg',
				'address_id' => $mainAddress->id,
			]);
		$secondCampus->phones()
		             ->attach($mainPhone->id, ['primary' => true]);

		for($i = 2; $i < 5; $i++)
		{
			$area = BuildingArea::create(
				[
					'building_id' => $secondCampus->id,
					'area_id' => $areaTable[$i],
					'blueprint_url' => $this->blueprints[$i]['blueprint_url'],
					'order' => ($i - 1),
				]);
			foreach($this->blueprints[$i]['rooms'] as $roomName => $room)
			{
				Room::create(
					[
						'name' => is_numeric($roomName) ? fake()->numberBetween(($i + 1) * 100,
							($i + 1) * 100 + 99) : $roomName,
						'capacity' => fake()->numberBetween(10, 100),
						'img_data' => json_decode($room),
						'phone_id' => Phone::factory()
						                   ->extensionOnly()
						                   ->create()->id,
						'area_id' => $area->id,
					]);
			}
		}
	}
}
