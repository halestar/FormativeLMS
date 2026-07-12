<?php

namespace Tests\Feature\Skills;

use App\Models\SubjectMatter\Assessment\Skill;
use App\Models\SubjectMatter\Assessment\SkillCategory;
use App\Models\SubjectMatter\Subject;
use App\Models\SystemTables\Level;
use Symfony\Component\HttpFoundation\Response;
use Tests\Feature\Support\InteractsWithServiceAccounts;
use Tests\TestCase;

class CreateTest extends TestCase
{
	use InteractsWithServiceAccounts;

	protected function show($id): string
	{
		return route('subjects.skills.show', ['skill' => $id]);
	}

	protected function store(): string
	{
		return route('subjects.skills.store');
	}

	protected function globalSkillData(): array
	{
		return
			[
				'skill_type'      => 'global',
				'subject_id'      => null,
				'designation'     => 'T1.1',
				'name'            => 'Test Skill',
				'category_id'     => null,
				'cat_designation' => null,
				'levels'          => Level::inRandomOrder()->take(3)->get()->pluck('id')->toArray(),
				'description'     => 'Test Skill Description',
			];
	}

	protected function subjectSkillData(): array
	{
		return
			[
				'skill_type'      => 'subject',
				'subject_id'      => Subject::inRandomOrder()->first()->id,
				'designation'     => 'T1.1',
				'name'            => 'Test Skill',
				'category_id'     => SkillCategory::inRandomOrder()->first()->id,
				'cat_designation' => "category designation",
				'levels'          => Level::inRandomOrder()->take(3)->get()->pluck('id')->toArray(),
				'description'     => 'Test Skill Description',
			];
	}

	public function test_store_admin(): void
	{
		$data = $this->globalSkillData();
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->store(), $data);

		$this->assertDatabaseHas('skills',
			[
				'designation' => $data['designation'],
				'name'        => $data['name'],
				'description' => $data['description'],
				'rubric'      => null,
				'global'      => true,
				'active'      => false,
			]);
		$newSkill = Skill::where('designation', $data['designation'])->first();
		$response->assertRedirect($this->show($newSkill->id));
	}

	public function test_store_staff(): void
	{
		$data = $this->globalSkillData();
		$response = $this->actingAsServiceAccount('staff@kalinec.net')
		                 ->post($this->store(), $data);

		$this->assertDatabaseHas('skills',
			[
				'designation' => $data['designation'],
				'name'        => $data['name'],
				'description' => $data['description'],
				'rubric'      => null,
				'global'      => true,
				'active'      => false,
			]);
		$newSkill = Skill::where('designation', $data['designation'])->first();
		$response->assertRedirect($this->show($newSkill->id));
	}

	public function test_store_faculty(): void
	{
		$data = $this->globalSkillData();
		$response = $this->actingAsServiceAccount('faculty@kalinec.net')
		                 ->post($this->store(), $data);

		$response->assertStatus(Response::HTTP_FORBIDDEN);
		$this->assertDatabaseMissing('skills',
			[
				'designation' => $data['designation'],
				'name'        => $data['name'],
			]);
	}

	public function test_store_student(): void
	{
		$data = $this->globalSkillData();
		$response = $this->actingAsServiceAccount('student@kalinec.net')
		                 ->post($this->store(), $data);

		$response->assertStatus(Response::HTTP_FORBIDDEN);
		$this->assertDatabaseMissing('skills',
			[
				'designation' => $data['designation'],
				'name'        => $data['name'],
			]);
	}

	public function test_store_parent(): void
	{
		$data = $this->globalSkillData();
		$response = $this->actingAsServiceAccount('parent@kalinec.net')
		                 ->post($this->store(), $data);

		$response->assertStatus(Response::HTTP_FORBIDDEN);
		$this->assertDatabaseMissing('skills',
			[
				'designation' => $data['designation'],
				'name'        => $data['name'],
			]);
	}

	public function test_store_guest(): void
	{
		$data = $this->globalSkillData();
		$response = $this->post($this->store(), $data);

		$response->assertRedirect(route('login'));
	}

	/**
	 * Tests that storing a skill fails when the skill type is missing.
	 */
	public function test_store_requires_skill_type(): void
	{
		$data = $this->globalSkillData();
		unset($data['skill_type']);
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->store(), $data);
		$response->assertRedirectBackWithErrors(['skill_type']);
		$this->assertDatabaseMissing('skills',
			[
				'designation' => $data['designation'],
				'name'        => $data['name'],
			]);
	}

	/**
	 * Tests that storing a skill fails when the skill type is not one of the allowed values.
	 */
	public function test_store_rejects_invalid_skill_type(): void
	{
		$data = $this->globalSkillData();
		$data['skill_type'] = 'invalid';
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->store(), $data);
		$response->assertRedirectBackWithErrors(['skill_type']);
		$this->assertDatabaseMissing('skills',
			[
				'designation' => $data['designation'],
				'name'        => $data['name'],
			]);
	}

	/**
	 * Tests that storing a subject skill fails when the subject ID is missing.
	 */
	public function test_store_requires_subject_id_for_subject_skills(): void
	{
		$data = $this->subjectSkillData();
		unset($data['subject_id']);
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->store(), $data);
		$response->assertRedirectBackWithErrors(['subject_id']);
		$this->assertDatabaseMissing('skills',
			[
				'designation' => $data['designation'],
				'name'        => $data['name'],
			]);
	}

	/**
	 * Tests that storing a subject skill fails when the subject ID does not exist.
	 */
	public function test_store_rejects_unknown_subject_id(): void
	{
		$data = $this->subjectSkillData();
		$data['subject_id'] = 100000;
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->store(), $data);
		$response->assertRedirectBackWithErrors(['subject_id']);
		$this->assertDatabaseMissing('skills',
			[
				'designation' => $data['designation'],
				'name'        => $data['name'],
			]);
	}

	/**
	 * Tests that storing a skill fails when the designation is missing.
	 */
	public function test_store_requires_designation(): void
	{
		$data = $this->globalSkillData();
		unset($data['designation']);
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->store(), $data);
		$response->assertRedirectBackWithErrors(['designation']);
		$this->assertDatabaseMissing('skills',
			[
				'name' => $data['name'],
			]);
	}

	/**
	 * Tests that storing a skill fails when the designation exceeds the maximum length.
	 */
	public function test_store_rejects_overlong_designation(): void
	{
		$data = $this->globalSkillData();
		$data['designation'] = str_repeat('x', 256);
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->store(), $data);
		$response->assertRedirectBackWithErrors(['designation']);
		$this->assertDatabaseMissing('skills',
			[
				'designation' => $data['designation'],
				'name'        => $data['name'],
			]);
	}

	/**
	 * Tests that storing a skill fails when the name exceeds the maximum length.
	 */
	public function test_store_rejects_overlong_name(): void
	{
		$data = $this->globalSkillData();
		$data['name'] = str_repeat('x', 256);
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->store(), $data);
		$response->assertRedirectBackWithErrors(['name']);
		$this->assertDatabaseMissing('skills',
			[
				'designation' => $data['designation'],
				'name'        => $data['name'],
			]);
	}

	/**
	 * Tests that storing a subject skill fails when the category ID is missing.
	 */
	public function test_store_requires_category_id_for_subject_skills(): void
	{
		$data = $this->subjectSkillData();
		unset($data['category_id']);
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->store(), $data);
		$response->assertRedirectBackWithErrors(['category_id']);
		$this->assertDatabaseMissing('skills',
			[
				'designation' => $data['designation'],
				'name'        => $data['name'],
			]);
	}

	/**
	 * Tests that storing a subject skill fails when the category ID does not exist.
	 */
	public function test_store_rejects_unknown_category_id(): void
	{
		$data = $this->subjectSkillData();
		$data['category_id'] = 100000;
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->store(), $data);
		$response->assertRedirectBackWithErrors(['category_id']);
		$this->assertDatabaseMissing('skills',
			[
				'designation' => $data['designation'],
				'name'        => $data['name'],
			]);
	}

	/**
	 * Tests that storing a subject skill fails when the category designation is missing.
	 */
	public function test_store_requires_cat_designation_for_subject_skills(): void
	{
		$data = $this->subjectSkillData();
		unset($data['cat_designation']);
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->store(), $data);
		$response->assertRedirectBackWithErrors(['cat_designation']);
		$this->assertDatabaseMissing('skills',
			[
				'designation' => $data['designation'],
				'name'        => $data['name'],
			]);
	}

	/**
	 * Tests that storing a subject skill fails when the category designation exceeds the maximum length.
	 */
	public function test_store_rejects_overlong_cat_designation(): void
	{
		$data = $this->subjectSkillData();
		$data['cat_designation'] = str_repeat('x', 256);
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->store(), $data);
		$response->assertRedirectBackWithErrors(['cat_designation']);
		$this->assertDatabaseMissing('skills',
			[
				'designation' => $data['designation'],
				'name'        => $data['name'],
			]);
	}

	/**
	 * Tests that storing a skill fails when the levels list is missing.
	 */
	public function test_store_requires_levels(): void
	{
		$data = $this->subjectSkillData();
		unset($data['levels']);
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->store(), $data);
		$response->assertRedirectBackWithErrors(['levels']);
		$this->assertDatabaseMissing('skills',
			[
				'designation' => $data['designation'],
				'name'        => $data['name'],
			]);
	}

	/**
	 * Tests that storing a skill fails when levels is not an array.
	 */
	public function test_store_requires_levels_to_be_array(): void
	{
		$data = $this->subjectSkillData();
		$data['levels'] = '1st';
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->store(), $data);
		$response->assertRedirectBackWithErrors(['levels']);
		$this->assertDatabaseMissing('skills',
			[
				'designation' => $data['designation'],
				'name'        => $data['name'],
			]);
	}

	/**
	 * Tests that storing a skill fails when no levels are provided.
	 */
	public function test_store_requires_at_least_one_level(): void
	{
		$data = $this->subjectSkillData();
		$data['levels'] = [];
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->store(), $data);
		$response->assertRedirectBackWithErrors(['levels']);
		$this->assertDatabaseMissing('skills',
			[
				'designation' => $data['designation'],
				'name'        => $data['name'],
			]);
	}

	/**
	 * Tests that storing a skill fails when a provided level ID does not exist.
	 */
	public function test_store_rejects_unknown_level_id(): void
	{
		$data = $this->subjectSkillData();
		$data['levels'] = [100000];
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->store(), $data);
		$response->assertRedirectBackWithErrors(['levels.0']);
		$this->assertDatabaseMissing('skills',
			[
				'designation' => $data['designation'],
				'name'        => $data['name'],
			]);
	}

	/**
	 * Tests that storing a skill fails when the description is missing.
	 */
	public function test_store_requires_description(): void
	{
		$data = $this->subjectSkillData();
		unset($data['description']);
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->store(), $data);
		$response->assertRedirectBackWithErrors(['description']);
		$this->assertDatabaseMissing('skills',
			[
				'designation' => $data['designation'],
				'name'        => $data['name'],
			]);
	}

	/**
	 * Tests that storing a global skill does not attach subject or category pivot rows.
	 */
	public function test_store_global_skill_does_not_attach_subject_or_category_relations(): void
	{
		$data = $this->globalSkillData();
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->store(), $data);
		$newSkill = Skill::where('designation', $data['designation'])->first();
		$this->assertDatabaseMissing('skill_category_designation', ['skill_id' => $newSkill->id]);
		$this->assertDatabaseMissing('skillables',
			[
				'skill_id'       => $newSkill->id,
				'skillable_type' => Subject::class,
			]);
	}

	/**
	 * Tests that storing a subject skill attaches its subject and category pivot rows.
	 */
	public function test_store_subject_skill_attaches_subject_and_category_relations(): void
	{
		$data = $this->subjectSkillData();
		$response = $this->actingAsServiceAccount('fablms@kalinec.net')
		                 ->post($this->store(), $data);
		$newSkill = Skill::where('designation', $data['designation'])->first();
		$this->assertDatabaseHas('skillables',
			[
				'skill_id'       => $newSkill->id,
				'skillable_type' => Subject::class,
				'skillable_id'   => $data['subject_id'],
			]);
		$this->assertDatabaseHas('skill_category_designation',
			[
				'skill_id'    => $newSkill->id,
				'category_id' => $data['category_id'],
				'designation' => $data['cat_designation'],
			]);
	}
}
