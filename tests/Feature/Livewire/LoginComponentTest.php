<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Auth\LoginForm;
use App\Models\People\Person;
use Database\Seeders\SmallDatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LoginComponentTest extends TestCase
{
	use RefreshDatabase;
	protected $seed = true;
	protected $seeder = SmallDatabaseSeeder::class;
    /**
     * A basic feature test example.
     */
    public function test_renders_successfully(): void
    {
	    Livewire::test(LoginForm::class)
	            ->assertStatus(200);
    }
	
	public function test_appears_on_page(): void
	{
		$this->actingAsGuest();
		$this->get(route('login', absolute: false))->assertSeeLivewire(LoginForm::class);
	}
	
	public function test_appears_with_cookie(): void
	{
		$user = Person::first();
		Livewire::withCookie('remember-me', $user->system_email)
			->test(LoginForm::class)
			->assertSet('email', $user->system_email)
			->assertSet('rememberMe', true);
	}
}
