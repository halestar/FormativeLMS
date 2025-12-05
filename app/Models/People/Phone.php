<?php

namespace App\Models\People;

use App\Models\Locations\Building;
use App\Models\Locations\Campus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Phone extends Model
{
	/** @use HasFactory<\Database\Factories\People\PhoneFactory> */
	use HasFactory;
	
	public $timestamps = true;
	protected $table = "phones";
	protected $primaryKey = "id";
	protected $fillable = ['phone', 'ext', 'mobile'];
	
	public function buildings(): MorphToMany
	{
		return $this->morphedByMany(Building::class, 'phoneable', 'phoneables')
		            ->using(PersonalPhone::class)
		            ->as('personal')
		            ->withPivot(
			            [
				            'primary', 'label', 'order',
			            ]);
	}
	
	public function prettyPhone(): Attribute
	{
		return Attribute::make
		(
			get: fn(mixed $value) => $this->pPhone()
		);
	}
	
	private function pPhone(): string
	{
        if(strlen($this->phone) == 10)
            $phone = "(" . substr($this->phone, 0, 3) . ") " .
                substr($this->phone, 3, 3) . "-" . substr($this->phone, 6);
        elseif(strlen($this->phone) == 7)
            $phone = substr($this->phone, 0, 3) . "-" . substr($this->phone, 4);
        elseif(strlen($this->phone) > 10)
            $phone = wordwrap(substr($this->phone, 0, -10), 2, "-", true) .
                "(" . substr($this->phone, -10, 3) . ") " .
                substr($this->phone, -7, 3) . "-" . substr($this->phone, -4);
        else
            $phone = $this->phone;
		if($this->ext)
			$phone .= " Ext." . $this->ext;
		return $phone;
	}
	
	public function canDelete(): bool
	{
		return ($this->people()
		             ->count() == 0 && $this->campuses()
		                                    ->count() == 0);
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
	
	protected function casts(): array
	{
		return
			[
				'mobile' => 'boolean',
			];
	}
	
}
