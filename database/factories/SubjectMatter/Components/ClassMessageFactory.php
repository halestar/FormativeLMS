<?php

namespace Database\Factories\SubjectMatter\Components;

use App\Models\Locations\Term;
use App\Models\People\Person;
use App\Models\People\StudentRecord;
use App\Models\SubjectMatter\ClassSession;
use App\Models\SubjectMatter\Components\ClassMessage;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SubjectMatter\Components\ClassMessage>
 */
class ClassMessageFactory extends Factory
{
	/**
	 * Define the model's default state.
	 *
	 * @return array<string, mixed>
	 */
	public function definition(): array
	{
		return [
			'message' => $this->faker->paragraph(),
		];
	}
	
	public function withSession(ClassSession $session): static
	{
		return $this->state(fn(array $attributes) => [
			'session_id' => $session->id,
		]);
	}
	
	public function withStudent(StudentRecord $student): static
	{
		return $this->state(fn(array $attributes) => [
			'student_id' => $student->id,
		]);
	}
	
	public function withPostedBy(Person $poster): static
	{
		return $this->state(fn(array $attributes) => [
			'person_id' => $poster->id,
		]);
	}
	
	public function randomDateInTerm(Term $term): static
	{
		$endTime = $term->term_end;
		if($endTime->isFuture())
			$endTime = Carbon::now();
		return $this->state(fn(array $attributes) => [
			'created_at' => $endTime->subDays(rand(1, $term->term_start->diffInDays($endTime)))
			                        ->addSeconds(rand(1, 86400)),
		]);
	}
	
	public function fromParent()
	{
		return $this->state(fn(array $attributes) => [
			'from_type' => ClassMessage::FROM_PARENT,
		]);
	}
	
	public function fromStudent()
	{
		return $this->state(fn(array $attributes) => [
			'from_type' => ClassMessage::FROM_STUDENT,
		]);
	}
	
	public function fromTeacher()
	{
		return $this->state(fn(array $attributes) => [
			'from_type' => ClassMessage::FROM_TEACHER,
		]);
	}
	
	public function fromAdmin()
	{
		return $this->state(fn(array $attributes) => [
			'from_type' => ClassMessage::FROM_ADMIN,
		]);
	}
}
