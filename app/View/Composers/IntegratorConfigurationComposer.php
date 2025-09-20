<?php

namespace App\View\Composers;

use App\Classes\SessionSettings;
use App\Models\Integrations\Integrator;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class IntegratorConfigurationComposer
{
	public Collection $integrators;
	public bool $menuOpen;
	public function __construct(SessionSettings $sessionSettings)
	{
		
		$this->integrators = Integrator::all();
		$this->menuOpen = $sessionSettings->get('integrationsMenuExpanded', true);
	}
	
	public function compose(View $view): void
	{
		$composerBreadcrumb =
			[
				__('system.menu.integrators') => route('integrators.index'),
			];
		$view->with('composerBreadcrumb', $composerBreadcrumb)
				->with('integrators', $this->integrators)
				->with('menuOpen', $this->menuOpen);
	}
}