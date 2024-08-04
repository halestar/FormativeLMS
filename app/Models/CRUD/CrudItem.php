<?php

namespace App\Models\CRUD;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

abstract class CrudItem extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];
    protected $fillable = ['name', 'order'];

    public function crudKey(): int
    {
        return $this->id;
    }

    public function crudName(): string
    {
        return $this->name;
    }

    public function crudOrder(): int
    {
        return $this->order;
    }

    public function setCrudName(string $name): void
    {
        $this->name = $name;
        $this->save();
    }

    public function setCrudOrder(int $order): void
    {
        $this->order = $order;
        $this->save();
    }

    public static function crudItems(): Collection
    {
        return self::orderBy('order')->get();
    }

    public static function crudModels(): array
    {
        return
        [
            Ethnicity::class,
            Title::class,
            Suffix::class,
            Honors::class,
            Gender::class,
            Pronouns::class
        ];
    }
    abstract public static function getCrudModel(): string;
    abstract public static function getCrudModelName(): string;
    abstract public function canDelete(): bool;
}
