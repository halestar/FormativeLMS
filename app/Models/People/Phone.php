<?php

namespace App\Models\People;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Phone extends Model
{
    /** @use HasFactory<\Database\Factories\People\PhoneFactory> */
    use HasFactory;

    protected  $fillable = [ 'phone', 'ext' ];

    protected function casts(): array
    {
        return
            [
                'mobile' => 'boolean',
            ];
    }
    public function people(): BelongsToMany
    {
        return $this->belongsToMany(Person::class, 'people_phones')
            ->using(PersonalPhone::class)
            ->as('personal')
            ->withPivot(
                [
                    'primary', 'work',
                ]);
    }

    public function pPhone(): string
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

}
