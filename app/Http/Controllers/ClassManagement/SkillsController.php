<?php

namespace App\Http\Controllers\ClassManagement;

use App\Http\Controllers\Controller;
use App\Models\CRUD\SkillCategoryDesignation;
use App\Models\SubjectMatter\Assessment\CharacterSkill;
use App\Models\SubjectMatter\Assessment\KnowledgeSkill;
use App\Models\SubjectMatter\Assessment\SkillCategory;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;

class SkillsController extends Controller implements HasMiddleware
{
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

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $breadcrumb = [ trans_choice('subjects.skills', 2) => "#" ];
        return view('subjects.skills.index', compact('breadcrumb'));
    }

    public function createKnowledge(SkillCategory $category)
    {
        $breadcrumb =
            [
                trans_choice('subjects.skills', 2) => route('subjects.skills.index'),
                __('subjects.skills.knowledge.new') => '#',
            ];
        return view('subjects.skills.knowledge', compact('breadcrumb', 'category'));
    }

    public function createCharacter(SkillCategory $category)
    {
        $breadcrumb =
            [
                trans_choice('subjects.skills', 2) => route('subjects.skills.index'),
                __('subjects.skills.character.new') => '#',
            ];
        return view('subjects.skills.character', compact('breadcrumb', 'category'));
    }

    public function storeKnowledge(Request $request)
    {
        $data = $request->validate([
            'subject_id' => 'required|numeric|exists:subjects,id',
            'designation' => 'required|max:255',
            'name' => 'nullable|max:255',
            'category_id' => 'required|exists:skill_categories,id',
            'cat_designation_id' => 'required|exists:crud_skill_category_designations,id',
            'levels' => 'required|array|min:1',
            'description' => 'required',
        ], static::errors());
        $kSkill = new KnowledgeSkill();
        $kSkill->subject_id = $data['subject_id'];
        $kSkill->designation = $data['designation'];
        $kSkill->name = $data['name'];
        $kSkill->description = $data['description'];
        $kSkill->save();
        //next we link the category.
        $catDesignation = SkillCategoryDesignation::find($data['cat_designation_id']);
        $category = SkillCategory::find($data['category_id']);
        $category->knowledgeSkills()->attach($kSkill->id, ['designation_id' => $catDesignation->id]);
        //next, we attach levels.
        foreach($data['levels'] as $level)
            $kSkill->levels()->attach($level);
        return redirect()->route('subjects.skills.show.knowledge', $kSkill)->with('success-status', __('subjects.skills.created'));
    }

    public function storeCharacter(Request $request)
    {
        $data = $request->validate([
            'designation' => 'required|max:255',
            'name' => 'nullable|max:255',
            'category_id' => 'required|exists:skill_categories,id',
            'cat_designation_id' => 'required|exists:crud_skill_category_designations,id',
            'levels' => 'required|array|min:1',
            'description' => 'required',
        ], static::errors());
        $cSkill = new CharacterSkill();
        $cSkill->designation = $data['designation'];
        $cSkill->name = $data['name'];
        $cSkill->description = $data['description'];
        $cSkill->save();
        //next we link the category.
        $catDesignation = SkillCategoryDesignation::find($data['cat_designation_id']);
        $category = SkillCategory::find($data['category_id']);
        $category->characterSkills()->attach($cSkill->id, ['designation_id' => $catDesignation->id]);
        //next, we attach levels.
        foreach($data['levels'] as $level)
            $cSkill->levels()->attach($level);
        return redirect()->route('subjects.skills.show.character', $cSkill)->with('success-status', __('subjects.skills.created'));
    }

    public function showKnowledge(KnowledgeSkill $skill)
    {
        $breadcrumb =
            [
                trans_choice('subjects.skills', 2) => route('subjects.skills.index'),
                $skill->designation => '#',
            ];
        return view('subjects.skills.knowledge-show', compact('breadcrumb', 'skill'));
    }

    public function showCharacter(CharacterSkill $skill)
    {
        $breadcrumb =
            [
                trans_choice('subjects.skills', 2) => route('subjects.skills.index'),
                $skill->designation => '#',
            ];
        return view('subjects.skills.character-show', compact('breadcrumb', 'skill'));
    }

    public function editKnowledge(Request $request, KnowledgeSkill $skill)
    {
        $breadcrumb =
            [
                trans_choice('subjects.skills', 2) => route('subjects.skills.index'),
                $skill->designation => route('subjects.skills.show.knowledge', $skill),
                __('common.edit') => '#',
            ];
        return view('subjects.skills.knowledge-edit', compact('breadcrumb', 'skill'));
    }

    public function editCharacter(Request $request, CharacterSkill $skill)
    {
        $breadcrumb =
            [
                trans_choice('subjects.skills', 2) => route('subjects.skills.index'),
                $skill->designation => route('subjects.skills.show.character', $skill),
                __('common.edit') => '#',
            ];
        return view('subjects.skills.character-edit', compact('breadcrumb', 'skill'));
    }

    public function updateKnowledge(Request $request, KnowledgeSkill $skill)
    {
        $data = $request->validate([
            'subject_id' => 'required|numeric|exists:subjects,id',
            'designation' => 'required|max:255',
            'name' => 'nullable|max:255',
            'levels' => 'required|array|min:1',
            'description' => 'required',
        ], static::errors());
        $skill->subject_id = $data['subject_id'];
        $skill->designation = $data['designation'];
        $skill->name = $data['name'];
        $skill->description = $data['description'];
        //can and should we activate?
        if($skill->canActivate() && $request->has('active'))
            $skill->active = true;
        $skill->save();
        $skill->levels()->sync($data['levels']);
        return redirect()->route('subjects.skills.show.knowledge', $skill)->with('success-status', __('subjects.skills.updated'));
    }

    public function updateCharacter(Request $request, CharacterSkill $skill)
    {
        $data = $request->validate([
            'designation' => 'required|max:255',
            'name' => 'nullable|max:255',
            'levels' => 'required|array|min:1',
            'description' => 'required',
        ], static::errors());
        $skill->designation = $data['designation'];
        $skill->name = $data['name'];
        $skill->description = $data['description'];
        //can and should we activate?
        if($skill->canActivate() && $request->has('active'))
            $skill->active = true;
        $skill->save();
        $skill->levels()->sync($data['levels']);
        return redirect()->route('subjects.skills.show.character', $skill)->with('success-status', __('subjects.skills.updated'));
    }

    public function linkKnowledgeCategory(Request $request, KnowledgeSkill $skill)
    {
        $data = $request->validate([
            'category_id' => 'required|numeric|exists:skill_categories,id',
            'cat_designation_id' => 'required|exists:crud_skill_category_designations,id',
        ], static::errors());
        $category = SkillCategory::findOrFail($data['category_id']);
        $catDesignation = SkillCategoryDesignation::findOrFail($data['cat_designation_id']);
        $category->knowledgeSkills()->attach($skill->id, ['designation_id' => $catDesignation->id]);
        return redirect()->route('subjects.skills.show.knowledge', $skill)->with('success-status', __('subjects.skills.updated'));
    }

    public function linkCharacterCategory(Request $request, CharacterSkill $skill)
    {
        $data = $request->validate([
            'category_id' => 'required|numeric|exists:skill_categories,id',
            'cat_designation_id' => 'required|exists:crud_skill_category_designations,id',
        ], static::errors());
        $category = SkillCategory::findOrFail($data['category_id']);
        $catDesignation = SkillCategoryDesignation::findOrFail($data['cat_designation_id']);
        $category->characterSkills()->attach($skill->id, ['designation_id' => $catDesignation->id]);
        return redirect()->route('subjects.skills.show.character', $skill)->with('success-status', __('subjects.skills.updated'));
    }

    public function unlinkKnowledgeCategory(Request $request, KnowledgeSkill $skill, SkillCategory $category)
    {
        $category->knowledgeSkills()->detach($skill->id);
        return redirect()->route('subjects.skills.show.knowledge', $skill)->with('success-status', __('subjects.skills.updated'));
    }

    public function unlinkCharacterCategory(Request $request, CharacterSkill $skill, SkillCategory $category)
    {
        $category->characterSkills()->detach($skill->id);
        return redirect()->route('subjects.skills.show.character', $skill)->with('success-status', __('subjects.skills.updated'));
    }

    public function knowledgeRubric(Request $request, KnowledgeSkill $skill)
    {
        $breadcrumb =
            [
                trans_choice('subjects.skills', 2) => route('subjects.skills.index'),
                $skill->designation => route('subjects.skills.show.knowledge', $skill),
                __('subjects.skills.rubric.builder') => '#',
            ];
        return view('subjects.skills.rubric', compact('breadcrumb', 'skill'));
    }

    public function characterRubric(Request $request, CharacterSkill $skill)
    {
        $breadcrumb =
            [
                trans_choice('subjects.skills', 2) => route('subjects.skills.index'),
                $skill->designation => route('subjects.skills.show.character', $skill),
                __('subjects.skills.rubric.builder') => '#',
            ];
        return view('subjects.skills.rubric', compact('breadcrumb', 'skill'));
    }

	public static function middleware()
	{
		return ['auth', 'permission:subjects.skills'];
	}
}
