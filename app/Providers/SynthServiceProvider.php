<?php

namespace App\Providers;

use App\Classes\Synths\AuthenticationDesignationSynth;
use App\Classes\Synths\ClassSessionLayoutManagerSynth;
use App\Classes\Synths\ClassTabsSynth;
use App\Classes\Synths\ClassTabSynth;
use App\Classes\Synths\ClassWidgetSynth;
use App\Classes\Synths\DocumentFileSynth;
use App\Classes\Synths\IdCardElementSynth;
use App\Classes\Synths\IdCardSynth;
use App\Classes\Synths\LmsStorageSynth;
use App\Classes\Synths\NameConstructorSynth;
use App\Classes\Synths\NameTokenSynth;
use App\Classes\Synths\RoleFieldSynth;
use App\Classes\Synths\RubricSynth;
use App\Classes\Synths\TopAnnouncementWidgetSynth;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class SynthServiceProvider extends ServiceProvider
{
	private array $synths =
		[
			AuthenticationDesignationSynth::class,
			ClassSessionLayoutManagerSynth::class,
			ClassTabsSynth::class,
			ClassTabSynth::class,
			ClassWidgetSynth::class,
			DocumentFileSynth::class,
			IdCardElementSynth::class,
			IdCardSynth::class,
			LmsStorageSynth::class,
			NameConstructorSynth::class,
			NameTokenSynth::class,
			RoleFieldSynth::class,
			RubricSynth::class,
			TopAnnouncementWidgetSynth::class,
		];
	
	/**
	 * Register services.
	 */
	public function register(): void
	{
		//
	}
	
	/**
	 * Bootstrap services.
	 */
	public function boot(): void
	{
		Livewire::propertySynthesizer($this->synths);
	}
}
