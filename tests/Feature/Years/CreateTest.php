<?php

namespace Tests\Feature\Years;

use App\Models\Locations\Campus;
use App\Models\Locations\Term;
use App\Models\Locations\Year;
use Symfony\Component\HttpFoundation\Response;
use Tests\Feature\Support\InteractsWithServiceAccounts;
use Tests\TestCase;

class CreateTest extends TestCase
{
	use InteractsWithServiceAccounts;

	protected function index(): string
	{
		return route('locations.years.index');
	}

	protected function store(): string
	{
		return route('locations.years.store');
	}

	protected function termStore($id): string
	{
		return route('locations.years.terms.store', $id);
	}

	protected function data(): array
	{
		return [
			'label'      => '2125-2126',
			'year_start' => '2125-09-01',
			'year_end'   => '2126-06-30',
		];
	}

	protected function termData(Year $year): array
	{
		return [
			'term_label' => 'New Term',
			'campus_id'  => Campus::inRandomOrder()->first()->id,
			'term_start' => $year->year_start,
			'term_end'   => $year->year_end,
		];
	}

	public function test_store_admin(): void
	{
		$data = $this->data();
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->store(), $data);

		$response->assertRedirectBack();
		$this->assertDatabaseHas('years', $data);
	}

	public function test_store_staff(): void
	{
		$data = $this->data();
		$response = $this->actingAsServiceAccount('staff@kalinec.net')
		                 ->post($this->store(), $data);

		$response->assertRedirectBack();
		$this->assertDatabaseHas('years', $data);
	}

	public function test_store_faculty(): void
	{
		$data = $this->data();
		$response = $this->actingAsServiceAccount('faculty@kalinec.net')
		                 ->post($this->store(), $data);
		$response->assertStatus(Response::HTTP_FORBIDDEN);
		$this->assertDatabaseMissing('years', $data);
	}

	public function test_store_student(): void
	{
		$data = $this->data();
		$response = $this->actingAsServiceAccount('student@kalinec.net')
		                 ->post($this->store(), $data);
		$response->assertStatus(Response::HTTP_FORBIDDEN);
		$this->assertDatabaseMissing('years', $data);
	}

	public function test_store_parent(): void
	{
		$data = $this->data();
		$response = $this->actingAsServiceAccount('parent@kalinec.net')
		                 ->post($this->store(), $data);
		$response->assertStatus(Response::HTTP_FORBIDDEN);
		$this->assertDatabaseMissing('years', $data);
	}

	public function test_store_guest(): void
	{
		$data = $this->data();
		$response = $this->post($this->store(), $data);

		$response->assertRedirect(route('login'));
	}

	public function test_store_term_admin(): void
	{
		$year = Year::inRandomOrder()->first();
		$data = $this->termData($year);
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->termStore($year->id), $data);

		$response->assertRedirectBack();
		$this->assertDatabaseHas('terms',
			[
				'label'      => $data['term_label'],
				'campus_id'  => $data['campus_id'],
				'term_start' => $data['term_start'],
				'term_end'   => $data['term_end'],
			]);
	}

	public function test_store_term_staff(): void
	{
		$year = Year::inRandomOrder()->first();
		$data = $this->termData($year);
		$response = $this->actingAsServiceAccount('staff@kalinec.net')
		                 ->post($this->termStore($year->id), $data);

		$response->assertRedirectBack();
		$this->assertDatabaseHas('terms',
			[
				'label'      => $data['term_label'],
				'campus_id'  => $data['campus_id'],
				'term_start' => $data['term_start'],
				'term_end'   => $data['term_end'],
			]);
	}

	public function test_store_term_faculty(): void
	{
		$year = Year::inRandomOrder()->first();
		$data = $this->termData($year);
		$response = $this->actingAsServiceAccount('faculty@kalinec.net')
		                 ->post($this->termStore($year->id), $data);
		$response->assertStatus(Response::HTTP_FORBIDDEN);
		$this->assertDatabaseMissing('terms',
			[
				'label'      => $data['term_label'],
				'campus_id'  => $data['campus_id'],
				'term_start' => $data['term_start'],
				'term_end'   => $data['term_end'],
			]);
	}

	public function test_store_term_student(): void
	{
		$year = Year::inRandomOrder()->first();
		$data = $this->termData($year);
		$response = $this->actingAsServiceAccount('student@kalinec.net')
		                 ->post($this->termStore($year->id), $data);
		$response->assertStatus(Response::HTTP_FORBIDDEN);
		$this->assertDatabaseMissing('terms',
			[
				'label'      => $data['term_label'],
				'campus_id'  => $data['campus_id'],
				'term_start' => $data['term_start'],
				'term_end'   => $data['term_end'],
			]);
	}

	public function test_store_term_parent(): void
	{
		$year = Year::inRandomOrder()->first();
		$data = $this->termData($year);
		$response = $this->actingAsServiceAccount('parent@kalinec.net')
		                 ->post($this->termStore($year->id), $data);
		$response->assertStatus(Response::HTTP_FORBIDDEN);
		$this->assertDatabaseMissing('terms',
			[
				'label'      => $data['term_label'],
				'campus_id'  => $data['campus_id'],
				'term_start' => $data['term_start'],
				'term_end'   => $data['term_end'],
			]);
	}

	public function test_store_term_guest(): void
	{
		$year = Year::inRandomOrder()->first();
		$data = $this->termData($year);
		$response = $this->actingAsServiceAccount('parent@kalinec.net')
		                 ->post($this->termStore($year->id), $data);
		$response->assertStatus(Response::HTTP_FORBIDDEN);
	}

	/**
	 * Test that storing a year requires a label.
	 */
	public function test_store_requires_label(): void
	{
		$data = $this->data();
		unset($data['label']);
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->store(), $data);

		$response->assertRedirectBackWithErrors(['label']);
		$this->assertDatabaseMissing('years', $data);
	}

	/**
	 * Test that storing a year limits the label to 255 characters.
	 */
	public function test_store_limits_label_to_255_characters(): void
	{
		$data = $this->data();
		$data['label'] = str_repeat('a', 256);
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->store(), $data);

		$response->assertRedirectBackWithErrors(['label']);
		$this->assertDatabaseMissing('years', $data);
	}

	/**
	 * Test that storing a year requires a year start date.
	 */
	public function test_store_requires_year_start(): void
	{
		$data = $this->data();
		unset($data['year_start']);
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->store(), $data);

		$response->assertRedirectBackWithErrors(['year_start']);
		$this->assertDatabaseMissing('years', $data);
	}

	/**
	 * Test that storing a year rejects a year start date after the year end date.
	 */
	public function test_store_rejects_year_start_after_year_end(): void
	{
		$data = $this->data();
		$data['year_start'] = '2126-07-01';
		$data['year_end'] = '2126-06-30';
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->store(), $data);

		$response->assertRedirectBackWithErrors(['year_start']);
		$this->assertDatabaseMissing('years', $data);
	}

	/**
	 * Test that storing a year requires a year end date.
	 */
	public function test_store_requires_year_end(): void
	{
		$data = $this->data();
		unset($data['year_end']);
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->store(), $data);

		$response->assertRedirectBackWithErrors(['year_end']);
		$this->assertDatabaseMissing('years', $data);
	}

	/**
	 * Test that storing a year rejects a year end date before the year start date.
	 */
	public function test_store_rejects_year_end_before_year_start(): void
	{
		$data = $this->data();
		$data['year_start'] = '2125-09-01';
		$data['year_end'] = '2125-08-31';
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->store(), $data);

		$response->assertRedirectBackWithErrors(['year_end']);
		$this->assertDatabaseMissing('years', $data);
	}

	/**
	 * Test that storing a term requires a term label.
	 */
	public function test_store_term_requires_term_label(): void
	{
		$year = Year::inRandomOrder()->first();
		$data = $this->termData($year);
		$initialCount = Term::count();
		unset($data['term_label']);
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->termStore($year->id), $data);

		$response->assertRedirectBackWithErrors(['term_label']);
		$this->assertSame($initialCount, Term::count());
	}

	/**
	 * Test that storing a term limits the term label to 255 characters.
	 */
	public function test_store_term_limits_term_label_to_255_characters(): void
	{
		$year = Year::inRandomOrder()->first();
		$data = $this->termData($year);
		$initialCount = Term::count();
		$data['term_label'] = str_repeat('a', 256);
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->termStore($year->id), $data);

		$response->assertRedirectBackWithErrors(['term_label']);
		$this->assertSame($initialCount, Term::count());
	}

	/**
	 * Test that storing a term requires a campus ID.
	 */
	public function test_store_term_requires_campus_id(): void
	{
		$year = Year::inRandomOrder()->first();
		$data = $this->termData($year);
		$initialCount = Term::count();
		unset($data['campus_id']);
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->termStore($year->id), $data);

		$response->assertRedirectBackWithErrors(['campus_id']);
		$this->assertSame($initialCount, Term::count());
	}

	/**
	 * Test that storing a term rejects an unknown campus ID.
	 */
	public function test_store_term_rejects_unknown_campus_id(): void
	{
		$year = Year::inRandomOrder()->first();
		$data = $this->termData($year);
		$initialCount = Term::count();
		$data['campus_id'] = 999999;
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->termStore($year->id), $data);

		$response->assertRedirectBackWithErrors(['campus_id']);
		$this->assertSame($initialCount, Term::count());
	}

	/**
	 * Test that storing a term requires a term start date.
	 */
	public function test_store_term_requires_term_start(): void
	{
		$year = Year::inRandomOrder()->first();
		$data = $this->termData($year);
		$initialCount = Term::count();
		unset($data['term_start']);
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->termStore($year->id), $data);

		$response->assertRedirectBackWithErrors(['term_start']);
		$this->assertSame($initialCount, Term::count());
	}

	/**
	 * Test that storing a term rejects a term start date before the year start date.
	 */
	public function test_store_term_rejects_term_start_before_year_start(): void
	{
		$year = Year::inRandomOrder()->first();
		$data = $this->termData($year);
		$initialCount = Term::count();
		$data['term_start'] = $year->year_start->copy()->subDay()->format(config('lms.date_format'));
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->termStore($year->id), $data);

		$response->assertRedirectBackWithErrors(['term_start']);
		$this->assertSame($initialCount, Term::count());
	}

	/**
	 * Test that storing a term rejects a term start date after the term end date.
	 */
	public function test_store_term_rejects_term_start_after_term_end(): void
	{
		$year = Year::inRandomOrder()->first();
		$data = $this->termData($year);
		$initialCount = Term::count();
		$data['term_start'] = $year->year_end->copy()->addDay()->format(config('lms.date_format'));
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->termStore($year->id), $data);

		$response->assertRedirectBackWithErrors(['term_start']);
		$this->assertSame($initialCount, Term::count());
	}

	/**
	 * Test that storing a term requires a term end date.
	 */
	public function test_store_term_requires_term_end(): void
	{
		$year = Year::inRandomOrder()->first();
		$data = $this->termData($year);
		$initialCount = Term::count();
		unset($data['term_end']);
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->termStore($year->id), $data);

		$response->assertRedirectBackWithErrors(['term_end']);
		$this->assertSame($initialCount, Term::count());
	}

	/**
	 * Test that storing a term rejects a term end date before the term start date.
	 */
	public function test_store_term_rejects_term_end_before_term_start(): void
	{
		$year = Year::inRandomOrder()->first();
		$data = $this->termData($year);
		$initialCount = Term::count();
		$data['term_end'] = $year->year_start->copy()->subDay()->format(config('lms.date_format'));
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->termStore($year->id), $data);

		$response->assertRedirectBackWithErrors(['term_end']);
		$this->assertSame($initialCount, Term::count());
	}

	/**
	 * Test that storing a term rejects a term end date after the year end date.
	 */
	public function test_store_term_rejects_term_end_after_year_end(): void
	{
		$year = Year::inRandomOrder()->first();
		$data = $this->termData($year);
		$initialCount = Term::count();
		$data['term_end'] = $year->year_end->copy()->addDay()->format(config('lms.date_format'));
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->termStore($year->id), $data);

		$response->assertRedirectBackWithErrors(['term_end']);
		$this->assertSame($initialCount, Term::count());
	}

	/**
	 * Test that storing a term attaches the new term to the selected year.
	 */
	public function test_store_term_attaches_term_to_year(): void
	{
		$year = Year::inRandomOrder()->first();
		$data = $this->termData($year);
		$initialCount = Term::count();
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->termStore($year->id), $data);

		$response->assertRedirectBack();
		$this->assertSame($initialCount + 1, Term::count());
		$this->assertDatabaseHas('terms',
			[
				'label'      => $data['term_label'],
				'campus_id'  => $data['campus_id'],
				'year_id'    => $year->id,
				'term_start' => $data['term_start'],
				'term_end'   => $data['term_end'],
			]);
	}
}
