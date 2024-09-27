<?php

namespace App\Models\People\ViewPolicies;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ViewPolicyField extends Pivot
{

    public $timestamps = false;
    protected $table = "view_policies_fields";
    protected function casts(): array
    {
        return
            [
                'editable' => 'boolean',
                'self_viewable' => 'boolean',
                'self_enforce' => 'boolean',
                'employee_viewable' => 'boolean',
                'employee_enforce' => 'boolean',
                'parent_viewable' => 'boolean',
                'parent_enforce' => 'boolean',
                'student_viewable' => 'boolean',
                'student_enforce' => 'boolean',
            ];
    }

    protected $fillable =
        [
            'editable',
            'self_viewable',
            'self_enforce',
            'employee_viewable',
            'employee_enforce',
            'parent_viewable',
            'parent_enforce',
            'student_viewable',
            'student_enforce',
        ];
}
