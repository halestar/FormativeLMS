<?php

namespace App\Http\Controllers\ClassManagement;

use App\Http\Controllers\Controller;
use App\Models\SubjectMatter\Assessment\CharacterSkill;
use App\Models\SubjectMatter\Assessment\Skill;
use App\Models\SubjectMatter\Assessment\SkillCategory;
use App\Models\SubjectMatter\Subject;
use App\Models\SystemTables\SkillCategoryDesignation;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;

class SkillsController extends Controller implements HasMiddleware
{
	public static function middleware()
	{
		return ['auth', 'permission:subjects.skills'];
	}
	
	/**
	 * Display a listing of the resource.
	 */
	public function index()
	{
		$breadcrumb = [trans_choice('subjects.skills', 2) => "#"];
		return view('subjects.skills.index', compact('breadcrumb'));
	}
	
	public function create(SkillCategory $category = null)
	{
		$breadcrumb =
			[
				trans_choice('subjects.skills', 2) => route('subjects.skills.index'),
				__('subjects.skills.new') => '#',
			];
		if($category)
			$parentCategories  = $category->parentCategory->subCategories;
		else
			$parentCategories = SkillCategory::root()->get();
		return view('subjects.skills.create', compact('breadcrumb', 'category', 'parentCategories'));
	}
	
	public function store(Request $request)
	{
		$data = $request->validate([
			'skill_type' => 'required|in:subject,global',
			'subject_id' => 'nullable|required_if:skill_type,subject|exists:subjects,id',
			'designation' => 'required|max:255',
			'name' => 'nullable|max:255',
			'category_id' => 'nullable|required_if:skill_type,subject|exists:skill_categories,id',
			'cat_designation' => 'nullable|required_if:skill_type,subject|max: 255',
			'levels' => 'required|array|min:1',
			'description' => 'required',
		], static::errors());
		$kSkill = new Skill();
		$kSkill->global = $data['skill_type'] == "global";
		$kSkill->designation = $data['designation'];
		$kSkill->name = $data['name'];
		$kSkill->description = $data['description'];
		$kSkill->save();
		//is this a global skill?
		if(!$kSkill->isGlobal())
		{
			$category = SkillCategory::find($data['category_id']);
			$category->skills()
			         ->attach($kSkill->id, ['designation' => $data['cat_designation']]);
			$kSkill->subjects()->attach($data['subject_id']);
		}
		//next, we attach levels.
		foreach($data['levels'] as $level)
			$kSkill->levels()
			       ->attach($level);
		return redirect()
			->route('subjects.skills.show', $kSkill)
			->with('success-status', __('subjects.skills.created'));
	}
	
	private static function errors(): array
	{
		return [
			'subject_id' => __('errors.skills.knowledge.subject_id'),
			'designation' => __('errors.skills.knowledge.designation'),
			'cat_designation_id' => __('errors.skills.knowledge.cat_designation_id'),
			'levels' => __('errors.skills.knowledge.levels'),
			'description' => __('errors.skills.knowledge.description'),
			'category_id' => __('errors.skills.knowledge.category_id'),
		];
	}
	
	public function show(Skill $skill)
	{
		$breadcrumb =
			[
				trans_choice('subjects.skills', 2) => route('subjects.skills.index'),
				$skill->designation => '#',
			];
		return view('subjects.skills.show', compact('breadcrumb', 'skill'));
	}
	
	public function edit(Request $request, Skill $skill)
	{
		$breadcrumb =
			[
				trans_choice('subjects.skills', 2) => route('subjects.skills.index'),
				$skill->designation => route('subjects.skills.show', $skill),
				__('common.edit') => '#',
			];
		return view('subjects.skills.edit', compact('breadcrumb', 'skill'));
	}
	
	public function update(Request $request, Skill $skill)
	{
		$rules =
			[
				'designation' => 'required|max:255',
				'name' => 'nullable|max:255',
				'levels' => 'required|array|min:1',
				'description' => 'required',
			];
		if(!$skill->isGlobal())
			$rules['subject_id'] = 'required|numeric|exists:subjects,id';
		$data = $request->validate($rules, static::errors());
		$skill->designation = $data['designation'];
		$skill->name = $data['name'];
		$skill->description = $data['description'];
		//can and should we activate?
		if($skill->canActivate() && $request->has('active'))
			$skill->active = true;
		$skill->save();
		$skill->levels()
		      ->sync($data['levels']);
		return redirect()
			->route('subjects.skills.show', $skill)
			->with('success-status', __('subjects.skills.updated'));
	}
	
	public function linkCategory(Request $request, Skill $skill)
	{
		$data = $request->validate([
			'category_id' => 'required|numeric|exists:skill_categories,id',
			'cat_designation' => 'required|max:255',
		], static::errors());
		$category = SkillCategory::findOrFail($data['category_id']);
		$category->skills()
		         ->attach($skill->id, ['designation' => $data['cat_designation']]);
		return redirect()
			->route('subjects.skills.show', $skill)
			->with('success-status', __('subjects.skills.updated'));
	}
	
	public function unlinkCategory(Request $request, Skill $skill, SkillCategory $category)
	{
		$category->skills()
		         ->detach($skill->id);
		return redirect()
			->route('subjects.skills.show', $skill)
			->with('success-status', __('subjects.skills.updated'));
	}
	
	public function rubric(Request $request, Skill $skill)
	{
		$breadcrumb =
			[
				trans_choice('subjects.skills', 2) => route('subjects.skills.index'),
				$skill->designation => route('subjects.skills.show', $skill),
				__('subjects.skills.rubric.builder') => '#',
			];
		return view('subjects.skills.rubric', compact('breadcrumb', 'skill'));
	}
	
	public function destroy(Skill $skill)
	{
		$skill->delete();
		return redirect()
			->route('subjects.skills.index')
			->with('success-status', __('subjects.skills.deleted'));
	}
	
	public function linkSubject(Skill $skill, Subject $subject)
	{
		$skill->subjects()->attach($subject->id);
		return redirect()
			->route('subjects.skills.edit', $skill)
			->with('success-status', __('subjects.skills.updated'));
	}
	
	public function unlinkSubject(Skill $skill, Subject $subject)
	{
		$skill->subjects()->detach($subject->id);
		return redirect()
			->route('subjects.skills.edit', $skill)
			->with('success-status', __('subjects.skills.updated'));
	}
}
