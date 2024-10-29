<?php

namespace App\Models\People\ViewPolicies;

use App\Models\CRUD\ViewableGroup;
use App\Models\People\Person;
use App\Models\Scopes\OrderByOrderScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

#[ScopedBy([OrderByOrderScope::class])]
class ViewableField extends Model
{

    public $timestamps = false;
    protected $table = "viewable_fields";
    public int $active_tab;
    protected $fillable =
        [
            'group_id',
            'name',
            'field',
            'parent_class',
            'format_as_date',
            'format_as_datetime',
            'order',
        ];

    protected function casts(): array
    {
        return
        [
            'format_as_datetime' => 'boolean',
            'format_as_date' => 'boolean',
        ];
    }

    public function policy(): BelongsToMany
    {
        return $this->belongsToMany(ViewPolicy::class, 'view_policies_fields', 'field_id', 'policy_id')
            ->using(ViewPolicyField::class)
            ->as('permissions')
            ->withPivot(
                [
                    'editable', 'self_viewable', 'self_enforce', 'employee_viewable',
                    'employee_enforce','parent_viewable','parent_enforce','student_viewable','student_enforce'
                ]);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(ViewableGroup::class, 'group_id');
    }

    public function fieldValue($obj)
    {
        if($obj instanceof $this->parent_class)
        {
            $field = $this->field;
            if($obj->$field && $this->format_as_date)
                return $obj->$field->format(config('lms.date_format'));
            if($obj->$field && $this->format_as_datetime)
                return $obj->$field->format(config('lms.datetime_format'));
            return $obj->$field;
        }
        return __('common.unknown');
    }
}
