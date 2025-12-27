<?php

namespace Tests\Feature\Livewire\SubjectMatter\Learning;

use App\Enums\SystemLogType;
use App\Livewire\Auth\LoginForm;
use App\Livewire\SubjectMatter\Learning\LearningDemonstrationEditor;
use App\Models\People\Person;
use App\Models\SubjectMatter\Assessment\Skill;
use App\Models\SubjectMatter\ClassSession;
use App\Models\SubjectMatter\Learning\ClassCriteria;
use App\Models\SubjectMatter\Learning\LearningDemonstration;
use App\Models\SubjectMatter\Learning\LearningDemonstrationClassSession;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Log;
use Livewire\Livewire;
use Tests\TestCase;

class LearningDemonstrationEditorComponentTest extends TestCase
{
	use DatabaseTransactions;

	private function createLd(Person $faculty, ClassSession $session): LearningDemonstration
	{
		$skill = Skill::active()->inRandomOrder()->first();
		return LearningDemonstration::factory()
			->for($faculty, 'owner')
			->hasAttached($skill, ['rubric' => $skill->rubric, 'weight' => 1])
			->has(LearningDemonstrationClassSession::factory()
				->count(1)
				->for($session, 'classSession')
				->for($session->classCriteria()->inRandomOrder()->first(), 'criteria')
				->post(), 'demonstrationSessions')
			->create()
			->refresh();
	}
	
	/**
	 * A basic feature test example.
	 */
	public function test_renders_successfully(): void
	{
		$faculty = Person::where('email', 'faculty@kalinec.net')->first();
		$session = $faculty->currentClassSessions()->first();
		$ld = $this->createLd($faculty, $session);
		$this->actingAs($faculty);
		Livewire::test(LearningDemonstrationEditor::class,
			[
				'ld' => $ld,
				'classSession' => $session,
			])
	        ->assertStatus(200);
	}
	
	public function test_appears_on_page(): void
	{
		$faculty = Person::where('email', 'faculty@kalinec.net')->first();
		$session = $faculty->currentClassSessions()->first();
		$ld = $this->createLd($faculty, $session);
		$this->actingAs($faculty);
		$this->get(route('learning.ld.edit',
			['ld' => $ld->id, 'classSession' => $session->id]))
		     ->assertSeeLivewire(LearningDemonstrationEditor::class);
	}

	public function test_delete(): void
	{
		$this->withoutDefer();
		$faculty = Person::where('email', 'faculty@kalinec.net')->first();
		$session = $faculty->currentClassSessions()->first();
		$ld = $this->createLd($faculty, $session);
		Livewire::actingAs($faculty)
			->test(LearningDemonstrationEditor::class, ['ld' => $ld, 'classSession' => $session])
			->call('deleteLearningDemonstration')
			->assertRedirect(route('subjects.school.classes.show', $session));
		$this->assertDatabaseMissing('learning_demonstrations', ['id' => $ld->id]);

	}

	public function test_change_name(): void
	{
		$faculty = Person::where('email', 'faculty@kalinec.net')->first();
		$session = $faculty->currentClassSessions()->first();
		$ld = $this->createLd($faculty, $session);
		$newName = 'New Name';
		Livewire::actingAs($faculty)
			->test(LearningDemonstrationEditor::class, ['ld' => $ld, 'classSession' => $session])
			->set('name', $newName)
			->call('updateLearningDemonstration')
			->assertRedirect(route('subjects.school.classes.show', $session));
		$this->assertDatabaseHas('learning_demonstrations', ['id' => $ld->id, 'name' => $newName])
			->assertDatabaseHas('system_logs', ['type' => SystemLogType::LearningDemonstration,
				'message' => __('logs.ld.update.name', ['old' => $ld->name, 'new' => $newName])]);

	}

	public function test_change_abbr(): void
	{
		$faculty = Person::where('email', 'faculty@kalinec.net')->first();
		$session = $faculty->currentClassSessions()->first();
		$ld = $this->createLd($faculty, $session);
		$newAbbr = 'New Abbr';
		Livewire::actingAs($faculty)
			->test(LearningDemonstrationEditor::class, ['ld' => $ld, 'classSession' => $session])
			->set('abbr', $newAbbr)
			->call('updateLearningDemonstration')
			->assertRedirect(route('subjects.school.classes.show', $session));
		$this->assertDatabaseHas('learning_demonstrations', ['id' => $ld->id, 'abbr' => $newAbbr])
			->assertDatabaseHas('system_logs', ['type' => SystemLogType::LearningDemonstration,
				'message' => __('logs.ld.update.abbr', ['old' => $ld->abbr, 'new' => $newAbbr])]);

	}

	public function test_change_demonstration(): void
	{
		$faculty = Person::where('email', 'faculty@kalinec.net')->first();
		$session = $faculty->currentClassSessions()->first();
		$ld = $this->createLd($faculty, $session);
		$demonstration = 'New Learning Objective';
		Livewire::actingAs($faculty)
			->test(LearningDemonstrationEditor::class, ['ld' => $ld, 'classSession' => $session])
			->set('demonstration', $demonstration)
			->call('updateLearningDemonstration')
			->assertRedirect(route('subjects.school.classes.show', $session));
		$this->assertDatabaseHas('learning_demonstrations', ['id' => $ld->id, 'demonstration' => $demonstration])
			->assertDatabaseHas('system_logs', ['type' => SystemLogType::LearningDemonstration,
				'message' => __('logs.ld.update.demonstration', ['old' => $ld->demonstration, 'new' => $demonstration])]);

	}

}
