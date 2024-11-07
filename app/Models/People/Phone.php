<?php

namespace App\Models\People;

use App\Models\Locations\Campus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Phone extends Model
{
    /** @use HasFactory<\Database\Factories\People\PhoneFactory> */
    use HasFactory;

    protected  $fillable = [ 'phone', 'ext', 'mobile' ];

    protected function casts(): array
    {
        return
            [
                'mobile' => 'boolean',
            ];
    }
    public function people(): MorphToMany
    {
        return $this->morphedByMany(Person::class, 'phoneable', 'phoneables')
            ->using(PersonalPhone::class)
            ->as('personal')
            ->withPivot(
                [
                    'primary', 'label', 'order',
                ]);
    }

    public function campuses(): MorphToMany
    {
        return $this->morphedByMany(Campus::class, 'phoneable', 'phoneables')
            ->using(PersonalPhone::class)
            ->as('personal')
            ->withPivot(
                [
                    'primary', 'label', 'order',
                ]);
    }

    private function pPhone(): string
    {
        $phone = $this->phone;
        if($this->ext)
            $phone .= " Ext." . $this->ext;
        return $phone;
    }

    public function prettyPhone(): Attribute
    {
        return Attribute::make
        (
            get: fn(mixed $value) => $this->pPhone()
        );
    }

    public function canDelete(): bool
    {
        return ($this->people()->count() == 0 && $this->campuses()->count() == 0);
    }

}
