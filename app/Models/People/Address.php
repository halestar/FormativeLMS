<?php

namespace App\Models\People;

use App\Models\People\ViewPolicies\ViewPolicyField;
use App\Traits\HasViewableFields;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Address extends Model
{
    use HasFactory, HasViewableFields;
    protected  $fillable = [ 'line1', 'line2', 'line3', 'city', 'state', 'zip', 'country' ];

    public static string $defaultCountry = "";

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        self::$defaultCountry = config('lms.default_country');
    }

    public function people(): BelongsToMany
    {
        return $this->belongsToMany(Person::class, 'people_addresses')
            ->using(PersonalAddress::class)
            ->as('personal')
            ->withPivot(
                [
                    'primary', 'work', 'seasonal',
                    'season_start','season_end',
                ]);
    }

    public function pAddress($includeCountry = true): string
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

    public function prettyAddress(): Attribute
    {
        return Attribute::make
        (
            get: fn(mixed $value) => $this->pAddress()
        );
    }
}
