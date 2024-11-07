<?php

namespace App\Models\Locations;

use App\Casts\LogItem;
use App\Models\CRUD\Level;
use App\Models\People\Address;
use App\Models\People\PersonalPhone;
use App\Models\People\Phone;
use App\Models\Scopes\OrderByOrderScope;
use App\Traits\Phoneable;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

#[ScopedBy(OrderByOrderScope::class)]
class Campus extends Model
{
    use Phoneable;
    public $timestamps = true;
    protected $table = "campuses";
    protected $primaryKey = "id";
    public $incrementing = true;
    protected $fillable =
        [
            'name',
            'abbr',
            'title',
            'established',
            'order',
            'img',
            'color_pri',
            'color_sec',
            'line1',
            'line2',
            'line3',
            'city',
            'state',
            'zip',
            'country',
        ];

    protected function casts(): array
    {
        return
            [
                'established' => 'date: Y',
            ];
    }

    public function levels(): BelongsToMany
    {
        return $this->belongsToMany(Level::class, 'campuses_levels', 'campus_id', 'level_id');
    }


    public function img(): Attribute
    {
        return Attribute::make
        (
            get: fn(?string $img) => $img?? asset('images/campus_img_placeholder.png'),
        );
    }

    public function canDelete(): bool
    {
        return true;
    }

    public function prettyAddress($includeCountry = true): string
    {
        $address = null;
        if($this->line1)
            $address = $this->line1;
        if($this->line2)
            $address = ($address? $address . "\n": "") . $this->line2;
        if($this->line3)
            $address = ($address? $address . "\n": "") . $this->line3;

        if($this->city)
            $address = ($address? $address . "\n": "") . $this->city;

        if($this->state)
        {
            if($this->city)
                $address .= ", " . $this->state;
            elseif($address)
                $address .= "\n" . $this->state;
            else
                $address = $this->state;
        }

        if($this->zip)
        {
            if($this->state)
                $address .= " " . $this->zip;
            elseif($this->city)
                $address .= ", " . $this->zip;
            elseif($address)
                $address .= "\n" . $this->zip;
            else
                $address .= $this->zip;
        }

        if($includeCountry)
        {
            if($address)
                $address .= "\n" . $this->country;
            else
                $address .= $this->country;
        }

        return $address;
    }

    public function canRemoveLevel(Level|int $level): bool
    {
        return true;
    }
}
