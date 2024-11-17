<?php

namespace App\Models\CRUD;

use App\Models\Scopes\OrderByOrderScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

#[ScopedBy([OrderByOrderScope::class])]
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

    public function scopeViewable(Builder $query): void
    {
        $query->whereNot('id', ViewableGroup::HIDDEN);
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
            Pronouns::class,
            ViewableGroup::class,
            Relationship::class,
            Level::class,
            SchoolArea::class,
        ];
    }
    abstract public static function getCrudModel(): string;
    abstract public static function getCrudModelName(): string;
    abstract public function canDelete(): bool;
    public function __toString():string
    {
        return $this->name;
    }

    public static function htmlOptions(CrudItem $selected = null): string
    {
        $html = "";
        foreach(self::crudItems() as $crudItem)
        {
            $html .= '<option value="' . $crudItem->crudKey() . '"';
            if($selected && $selected->crudKey() === $crudItem->crudKey())
                $html .= ' selected';
            $html .= '>' . $crudItem->crudName() . '</option>';
        }
        return $html;
    }
}
