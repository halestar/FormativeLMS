<?php

namespace App\Models\People\ViewPolicies;

use App\Enums\PolicyType;
use App\Models\CRUD\ViewableGroup;
use App\Models\People\Person;
use App\Models\Utilities\SchoolRoles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Role;

class ViewPolicy extends Model
{
    protected $table = 'view_policies';
    public $timestamps = true;
    protected $fillable = ['name', 'role_id', 'base_role'];
    protected function casts(): array
    {
        return
            [
                'base_role' => PolicyType::class,
            ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function  isBasePolicy(): bool
    {
        return $this->role_id == SchoolRoles::EmployeeRole()->id ||
            $this->role_id == SchoolRoles::ParentRole()->id ||
            $this->role_id == SchoolRoles::StudentRole()->id;
    }

    public function fields(): BelongsToMany
    {
        return $this->belongsToMany(ViewableField::class, 'view_policies_fields', 'policy_id', 'field_id')
            ->using(ViewPolicyField::class)
            ->as('permissions')
            ->withPivot(
                [
                    'editable', 'self_viewable', 'employee_viewable',
                    'employee_enforce','parent_viewable','parent_enforce','student_viewable','student_enforce'
                ]);
    }

    public function viewableFields(Person $viewer, ViewableGroup $group = null): Collection
    {
        $query = $this->fields();
        if($group)
            $query->where('group_id', $group->id);
        if($viewer->isEmployee())
        {
            //get fields from which the employee viewable is true
            $query->where('employee_viewable', true);
        }
        elseif($viewer->isStudent())
        {
            $query->where('student_viewable', true);
        }
        elseif ($viewer->isParent())
        {
            $query->where('parent_viewable', true);
        }
        return $query->get();
    }

    public function canView(Person $viewer, ViewableField $field): bool
    {
        if($viewer->isEmployee() && $field->employee_viewable)
            return true;
        if($viewer->isStudent() && $field->student_viewable)
            return true;
        if($viewer->isParent() && $field->parent_viewable)
            return true;
        return false;
    }

}
