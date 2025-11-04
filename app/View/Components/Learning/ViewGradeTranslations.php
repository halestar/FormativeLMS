<?php

namespace App\View\Components\Learning;

use App\Models\SubjectMatter\Learning\GradeTranslationSchema;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ViewGradeTranslations extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(public GradeTranslationSchema $schema){}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.learning.view-grade-translations');
    }
}
