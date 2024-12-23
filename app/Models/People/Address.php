<?php

namespace App\Models\People;

use App\Models\Locations\Building;
use App\Models\Locations\Campus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Address extends Model
{
    use HasFactory;
    protected  $fillable = [ 'line1', 'line2', 'line3', 'city', 'state', 'zip', 'country' ];
    public $timestamps = true;
    protected $table = "addresses";
    protected $primaryKey = "id";
    public static string $defaultCountry = "";

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        self::$defaultCountry = config('lms.default_country');
    }

    public function people(): MorphToMany
    {
        return $this->morphedByMany(Person::class, 'addressable', 'addressable')
            ->using(PersonalAddress::class)
            ->as('personal')
            ->withPivot(
                [
                    'primary', 'label', 'order',
                ]);
    }

    public function campus(): HasMany
    {
        return $this->hasMany(Campus::class, 'address_id');
    }

    public function buildings(): HasMany
    {
        return $this->hasMany(Building::class, 'address_id');
    }

    public function canDelete()
    {
        if($this->people()->count() > 0)
            return false;
        if($this->campus()->count() > 0)
            return false;
        return true;
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
