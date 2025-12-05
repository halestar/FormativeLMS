<?php

namespace App\Interfaces;

interface Synthesizable
{
    public function toArray(): array;
    public static function hydrate(array $data): static;
}
