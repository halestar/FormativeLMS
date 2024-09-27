<?php

namespace App\Livewire;

use App\Models\CRUD\ViewableGroup;
use App\Models\People\ViewPolicies\ViewableField;
use App\Models\People\ViewPolicies\ViewPolicy;
use Livewire\Component;

class ViewPermissionEditor extends Component
{
    public ViewPolicy $viewPolicy;
    public int $active_tab;
    public function mount(ViewPolicy $viewPolicy)
    {
        $this->active_tab = ViewableGroup::BASIC_INFO;
        $this->viewPolicy = $viewPolicy->fresh();
        $this->viewPolicy->load(['role', 'fields']);
    }
    public function updateFieldOrder($models)
    {
        foreach ($models as $model)
        {
            $field = ViewableField::find($model['value']);
            $field->order = $model['order'];
            $field->save();
        }
        $this->viewPolicy->refresh();
    }

    public function attachField(int $field)
    {
        $this->viewPolicy->fields()->attach($field);
        $this->viewPolicy->refresh();
    }

    public function dettachField(int $field)
    {
        $this->viewPolicy->fields()->detach($field);
        $this->viewPolicy->refresh();
    }

    public function toggleField(ViewableField $viewableField, string $field)
    {
        $policyField = $this->viewPolicy->fields->where('id', $viewableField->id)->first();
        $policyField->permissions->$field = !$policyField->permissions->$field;
        if($field == 'employee_enforce' && !$policyField->permissions->employee_enforce)
            $policyField->permissions->employee_viewable = false;
        elseif($field == 'student_enforce' && !$policyField->permissions->student_enforce)
            $policyField->permissions->student_viewable = false;
        elseif($field == 'parent_enforce' && !$policyField->permissions->parent_enforce)
            $policyField->permissions->parent_viewable = false;

        $policyField->permissions->save();
        $this->viewPolicy->refresh();
    }

    public function changeTab(int $newTab)
    {
        $this->active_tab = $newTab;
    }
    public function render()
    {
        return view('livewire.view-permission-editor');
    }
}
