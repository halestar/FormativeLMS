<?php

namespace Feature\Livewire\Auth;

use App\Enums\Auth\LoginStages;
use App\Livewire\Auth\LoginForm;
use App\Models\Integrations\IntegrationService;
use App\Models\People\Person;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

class LoginComponentTest extends TestCase
{
    use DatabaseTransactions;

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
        $this->get(route('login', absolute: false))
            ->assertSeeLivewire(LoginForm::class);
    }

    public function test_appears_with_cookie(): void
    {
        $user = Person::factory()->create();
        Livewire::withCookie('remember-me', $user->email)
            ->test(LoginForm::class)
            ->assertSet('email', $user->email)
            ->assertSet('rememberMe', true);
    }

    public function test_submit_email_validation_error(): void
    {
        Livewire::test(LoginForm::class)
            ->set('email', 'not-an-email')
            ->call('submitEmail')
            ->assertHasErrors(['email' => 'email']);
    }

    public function test_submit_email_does_not_exist(): void
    {
        Livewire::test(LoginForm::class)
            ->set('email', 'non-existent@example.com')
            ->call('submitEmail')
            ->assertHasErrors(['email' => 'exists']);
    }

    public function test_submit_email_transitions_to_password_stage(): void
    {
        $user = Person::factory()->create();
        $user->assignRole('Super Admin');
        $service = IntegrationService::where('className', 'App\Classes\Integrators\Local\Services\LocalAuthService')->first();
        $service->integrator->assignRole('Super Admin');

        $connection = $service->connect($user);
        $user->authConnection()->associate($connection);
        $user->save();

        Livewire::test(LoginForm::class)
            ->set('email', $user->email)
            ->call('submitEmail')
            ->assertSet('stage', LoginStages::PromptPassword)
            ->assertSet('user.id', $user->id);
    }

    public function test_submit_password_incorrect(): void
    {
        $user = Person::factory()->create();
        $user->assignRole('Super Admin');
        $service = IntegrationService::where('className', 'App\Classes\Integrators\Local\Services\LocalAuthService')->first();
        $service->integrator->assignRole('Super Admin');

        $connection = $service->connect($user);
        $connection->setPassword('password123');
        $user->authConnection()->associate($connection);
        $user->save();

        Livewire::test(LoginForm::class)
            ->set('email', $user->email)
            ->call('submitEmail')
            ->set('password', 'wrong-password')
            ->call('submitPassword')
            ->assertHasErrors(['password'])
            ->assertSet('password', '');
    }

    public function test_submit_password_correct_transitions_to_reset_if_required(): void
    {
        $user = Person::factory()->create();
        $user->assignRole('Super Admin');
        $service = IntegrationService::where('className', 'App\Classes\Integrators\Local\Services\LocalAuthService')->first();
        $service->integrator->assignRole('Super Admin');

        $connection = $service->connect($user);
        $connection->setPassword('password123');
        $connection->setMustChangePassword(true);
        $user->authConnection()->associate($connection);
        $user->save();

        Livewire::test(LoginForm::class)
            ->set('email', $user->email)
            ->call('submitEmail')
            ->set('password', 'password123')
            ->call('submitPassword')
            ->assertSet('stage', LoginStages::ResetPassword);
    }

    public function test_submit_email_transitions_to_method_selection(): void
    {
        $user = Person::factory()->create();
        $user->assignRole('Super Admin');

        $service1 = IntegrationService::where('className', 'App\Classes\Integrators\Local\Services\LocalAuthService')->first();

        $this->mock(\App\Classes\Settings\AuthSettings::class, function ($mock) use ($service1) {
            $mock->shouldReceive('determineAuthentication')
                ->andReturn(collect([$service1, clone $service1]));
        });

        Livewire::test(LoginForm::class)
            ->set('email', $user->email)
            ->call('submitEmail')
            ->assertSet('stage', LoginStages::PromptMethod);
    }

    public function test_submit_method_connects_and_reprocesses(): void
    {
        $user = Person::factory()->create();
        $user->assignRole('Super Admin');

        $service1 = IntegrationService::where('className', 'App\Classes\Integrators\Local\Services\LocalAuthService')->first();
        $service1->integrator->assignRole('Super Admin');

        Livewire::test(LoginForm::class)
            ->set('email', $user->email)
            ->set('user', $user)
            ->set('stage', LoginStages::PromptMethod)
            ->call('submitMethod', $service1)
            ->assertSet('stage', LoginStages::PromptPassword);

        $this->assertNotNull($user->fresh()->auth_connection_id);
    }

    public function test_forgot_password_transitions_to_verification(): void
    {
        $user = Person::factory()->create();
        $user->assignRole('Super Admin');
        $service = IntegrationService::where('className', 'App\Classes\Integrators\Local\Services\LocalAuthService')->first();
        $service->integrator->assignRole('Super Admin');

        $connection = $service->connect($user);
        $user->authConnection()->associate($connection);
        $user->save();

        $component = Livewire::test(LoginForm::class)
            ->set('email', $user->email)
            ->call('submitEmail')
            ->call('forgotPassword');

        $component->assertSet('stage', LoginStages::CodeVerification);
        $this->assertNotEmpty($component->get('authCode'));
    }

    public function test_submit_verification_correct_code(): void
    {
        $user = Person::factory()->create();
        $user->assignRole('Super Admin');
        $service = IntegrationService::where('className', 'App\Classes\Integrators\Local\Services\LocalAuthService')->first();
        $service->integrator->assignRole('Super Admin');

        $connection = $service->connect($user);
        $user->authConnection()->associate($connection);
        $user->save();

        $component = Livewire::test(LoginForm::class)
            ->set('email', $user->email)
            ->call('submitEmail')
            ->call('forgotPassword');

        $authCode = $component->get('authCode');

        $component->set('userAuthCode', $authCode)
            ->call('submitVerification')
            ->assertSet('stage', LoginStages::ResetPassword);
    }

    public function test_submit_verification_incorrect_code(): void
    {
        $user = Person::factory()->create();
        $user->assignRole('Super Admin');
        $service = IntegrationService::where('className', 'App\Classes\Integrators\Local\Services\LocalAuthService')->first();
        $service->integrator->assignRole('Super Admin');

        $connection = $service->connect($user);
        $user->authConnection()->associate($connection);
        $user->save();

        Livewire::test(LoginForm::class)
            ->set('email', $user->email)
            ->call('submitEmail')
            ->call('forgotPassword')
            ->set('userAuthCode', 'wrong-code')
            ->call('submitVerification')
            ->assertHasErrors(['userAuthCode'])
            ->assertSet('stage', LoginStages::CodeVerification);
    }

    public function test_verification_timeout(): void
    {
        $user = Person::factory()->create();
        $user->assignRole('Super Admin');
        $service = IntegrationService::where('className', 'App\Classes\Integrators\Local\Services\LocalAuthService')->first();
        $service->integrator->assignRole('Super Admin');

        $connection = $service->connect($user);
        $user->authConnection()->associate($connection);
        $user->save();

        Livewire::test(LoginForm::class)
            ->set('email', $user->email)
            ->call('submitEmail')
            ->call('forgotPassword')
            ->call('timeoutTimer')
            ->assertSet('stage', LoginStages::CodeTimeout);
    }
}
