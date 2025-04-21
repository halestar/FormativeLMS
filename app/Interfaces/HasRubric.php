<?php

namespace App\Interfaces;

use App\Casts\Rubric;

interface HasRubric
{
    public function getRubric();
    public function setRubric(Rubric $rubric);
    public function getDescription(): string;
    public function getSkillId(): int;
}
