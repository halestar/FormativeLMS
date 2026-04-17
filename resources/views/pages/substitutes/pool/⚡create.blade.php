<?php

use App\Models\Locations\Campus;
use App\Models\People\Person;
use App\Models\Utilities\SchoolRoles;
use App\Notifications\Substitutes\NewSubstituteVerification;
use App\Traits\FullPageComponent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
	use FullPageComponent;

	public string $first = '';
	public string $last = '';
	public string $email = '';
	public array $campusIds = [];
	public ?int $selectedPersonId = null;
	public bool $confirmCreateNewPerson = false;
	public Collection $campuses;

	public function mount(): void
	{
		abort_unless(auth()->user()?->can('substitute.admin'), 403);

		$this->campuses = Campus::query()
			->orderBy('name')
			->get([
				'id',
				'name'
			]);

		$this->breadcrumb = [
			__('features.features') => '#',
			trans_choice('features.substitutes.requests', 2) => route('features.substitutes.index'),
			__('features.substitutes.pool') => route('features.substitutes.pool.index'),
			__('features.substitutes.pool.new') => '#',
		];
	}

	#[Computed]
	public function selectedPerson(): ?Person
	{
		if (!$this->selectedPersonId)
		{
			return null;
		}

		return Person::withTrashed()
			->with('substituteProfile.campuses')
			->find($this->selectedPersonId);
	}

	#[Computed]
	public function matchingPeople(): Collection
	{
		$first = trim($this->first);
		$last = trim($this->last);
		$email = trim($this->email);

		if (strlen($first) < 2 && strlen($last) < 2 && strlen($email) < 3)
		{
			return collect();
		}

		$query = Person::query()
			->withTrashed()
			->where(function (Builder $query) use ($first, $last, $email)
			{
				$query->whereNull('id');

				if ($email !== '')
				{
					$query->orWhere('email', 'like', '%' . $email . '%');
				}

				if (strlen($first) >= 2)
				{
					$query->orWhere('first', 'like', '%' . $first . '%')
						->orWhere('nick', 'like', '%' . $first . '%');
				}

				if (strlen($last) >= 2)
				{
					$query->orWhere('last', 'like', '%' . $last . '%');
				}

				if (strlen($first) >= 2 && strlen($last) >= 2)
				{
					$query->orWhere(function (Builder $query) use ($first, $last)
					{
						$query->where('first', 'like', '%' . $first . '%')
							->where('last', 'like', '%' . $last . '%');
					});
				}
			})
			->limit(8);

		if ($email !== '')
		{
			$query->orderByRaw('LOWER(email) = ? desc', [mb_strtolower($email)]);
		}

		return $query->orderBy('last')
			->orderBy('first')
			->get();
	}

	public function selectExistingPerson(int $personId): void
	{
		$person = Person::withTrashed()
			->with('substituteProfile.campuses')
			->findOrFail($personId);

		$this->first = (string)($person->first ?? '');
		$this->last = (string)$person->last;
		$this->email = (string)($person->email ?? '');
		$this->selectedPersonId = $person->id;
		$this->confirmCreateNewPerson = false;

		if ($person->substituteProfile)
		{
			$this->campusIds = $person->substituteProfile->campuses
				->pluck('id')
				->map(fn (mixed $id) => (string)$id)
				->all();
			$this->sendVerificationEmail = !$person->substituteProfile->email_confirmed
			                               && !$person->substituteProfile->account_verified;
		}
	}

	public function clearSelectedPerson(): void
	{
		$this->selectedPersonId = null;
		$this->confirmCreateNewPerson = false;
		$this->sendVerificationEmail = true;
	}

	public function updatedFirst(): void
	{
		$this->handleIdentityFieldUpdate();
	}

	public function updatedLast(): void
	{
		$this->handleIdentityFieldUpdate();
	}

	public function updatedEmail(): void
	{
		$this->handleIdentityFieldUpdate();
	}

	public function save(): mixed
	{
		abort_unless(auth()->user()?->can('substitute.admin'), 403);

		$this->validate();
		$this->guardAgainstDuplicatePeople();

		$selectedPersonId = $this->selectedPersonId;
		$person = null;
		$substitute = null;
		$usedExistingPerson = false;
		$createdProfile = false;

		DB::transaction(function () use (
			&$person, &$substitute, &$usedExistingPerson, &$createdProfile, $selectedPersonId
		)
		{
			if ($selectedPersonId)
			{
				$person = Person::withTrashed()->findOrFail($selectedPersonId);
				$usedExistingPerson = true;

				if ($person->trashed())
				{
					$person->restore();
				}
			}
			else
			{
				$person = new Person();
				$person->first = trim($this->first);
				$person->last = trim($this->last);
				$person->email = trim($this->email);
				$person->save();
			}

			$substitute = $person->substituteProfile()->firstOrCreate([]);
			$createdProfile = $substitute->wasRecentlyCreated;

			$substitute->campuses()->sync($this->campusIds);

			if ($person->hasRole(SchoolRoles::$OLD_SUBSTITUTE))
			{
				$person->removeRole(SchoolRoles::$OLD_SUBSTITUTE);
			}

			if (!$person->hasRole(SchoolRoles::$SUBSTITUTE))
			{
				$person->assignRole(SchoolRoles::$SUBSTITUTE);
			}
		});

		$person->notify(new NewSubstituteVerification($person));

		return redirect()->route('features.substitutes.pool.show', $substitute->person->school_id)
			->with('success-status', __('features.substitutes.pool.created'));
	}

	protected function rules(): array
	{
		return [
			'first' => [
				'required',
				'string',
				'max:255'
			],
			'last' => [
				'required',
				'string',
				'max:255'
			],
			'email' => [
				'required',
				'email',
				'max:255'
			],
			'campusIds' => [
				'required',
				'array',
				'min:1'
			],
			'campusIds.*' => [
				'required',
				'exists:campuses,id'
			],
			'confirmCreateNewPerson' => ['boolean'],
			'sendVerificationEmail' => ['boolean'],
		];
	}

	private function handleIdentityFieldUpdate(): void
	{
		$this->confirmCreateNewPerson = false;

		$selectedPerson = $this->selectedPerson;
		if (!$selectedPerson)
		{
			return;
		}

		if (
			trim((string)($selectedPerson->first ?? '')) !== trim($this->first)
			|| trim((string)$selectedPerson->last) !== trim($this->last)
			|| mb_strtolower(trim((string)($selectedPerson->email ?? ''))) !== mb_strtolower(trim($this->email))
		)
		{
			$this->selectedPersonId = null;
			$this->sendVerificationEmail = true;
		}
	}

	private function guardAgainstDuplicatePeople(): void
	{
		if ($this->selectedPersonId)
		{
			return;
		}

		$exactEmailMatch = Person::withTrashed()
			->whereRaw('LOWER(email) = ?', [mb_strtolower(trim($this->email))])
			->first();

		if ($exactEmailMatch)
		{
			throw ValidationException::withMessages([
				'email' => __('features.substitutes.pool.create.validation.email_exists'),
			]);
		}

		if ($this->matchingPeople->isNotEmpty() && !$this->confirmCreateNewPerson)
		{
			throw ValidationException::withMessages([
				'confirmCreateNewPerson' => __('features.substitutes.pool.create.validation.confirm_new_person'),
			]);
		}
	}
};
?>

<div class="container py-4">
    @php($selectedPerson = $this->selectedPerson)
    @php($matchingPeople = $this->matchingPeople)

    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
        <div>
            <h1 class="h3 mb-1">{{ __('features.substitutes.pool.new') }}</h1>
            <p class="text-muted mb-0">{{ __('features.substitutes.pool.create.description') }}</p>
        </div>
        <a href="{{ route('features.substitutes.pool.index') }}" class="btn btn-outline-secondary">{{ __('features.substitutes.pool.back') }}</a>
    </div>

    <form wire:submit="save">
        <div class="row g-4 mb-4">
            <div class="col-12 col-xl-7">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                            <div>
                                <h2 class="h5 mb-1">{{ __('features.substitutes.pool.create.person') }}</h2>
                                <p class="text-muted small mb-0">{{ __('features.substitutes.pool.create.person.description') }}</p>
                            </div>
                            @if ($selectedPerson)
                                <button type="button" class="btn btn-sm btn-outline-secondary"
                                        wire:click="clearSelectedPerson">
                                    {{ __('features.substitutes.pool.create.person.clear') }}
                                </button>
                            @endif
                        </div>

                        @if($selectedPerson)
                            <div class="alert {{ $selectedPerson->substituteProfile ? 'alert-warning' : 'alert-info' }} d-flex flex-column gap-2 mb-4"
                                 role="alert">
                                <div class="fw-semibold">{{ __('features.substitutes.pool.create.person.selected', ['name' => $selectedPerson->name]) }}</div>
                                <div class="small">
                                    {{ $selectedPerson->email ?: __('features.substitutes.pool.create.person.no_email_on_file') }}
                                    @if ($selectedPerson->trashed())
                                        <span class="badge text-bg-warning ms-2">{{ __('features.substitutes.pool.create.badges.soft_deleted') }}</span>
                                    @endif
                                    @if ($selectedPerson->substituteProfile)
                                        <span class="badge text-bg-secondary ms-2">{{ __('features.substitutes.pool.create.badges.has_profile') }}</span>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label for="substitute-first" class="form-label">{{ __('features.substitutes.pool.edit.person.first') }}</label>
                                <input
                                        id="substitute-first"
                                        type="text"
                                        class="form-control @error('first') is-invalid @enderror"
                                        wire:model.live.debounce.300ms="first"
                                        @disabled($selectedPerson !== null)
                                        autocomplete="off"
                                >
                                @error('first')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="substitute-last" class="form-label">{{ __('features.substitutes.pool.edit.person.last') }}</label>
                                <input
                                        id="substitute-last"
                                        type="text"
                                        class="form-control @error('last') is-invalid @enderror"
                                        wire:model.live.debounce.300ms="last"
                                        @disabled($selectedPerson !== null)
                                        autocomplete="off"
                                >
                                @error('last')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="substitute-email" class="form-label">{{ __('features.substitutes.pool.create.person.email_address') }}</label>
                                <input
                                        id="substitute-email"
                                        type="email"
                                        class="form-control @error('email') is-invalid @enderror"
                                        wire:model.live.debounce.300ms="email"
                                        @disabled($selectedPerson !== null)
                                        autocomplete="off"
                                >
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-5">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                            <div>
                                <h2 class="h5 mb-1">{{ __('features.substitutes.pool.create.matches') }}</h2>
                                <p class="text-muted small mb-0">{{ __('features.substitutes.pool.create.matches.description') }}</p>
                            </div>
                            @if ($matchingPeople->isNotEmpty() && ! $selectedPerson)
                                <span class="badge text-bg-secondary">{{ __('features.substitutes.pool.create.matches.count', ['count' => $matchingPeople->count()]) }}</span>
                            @endif
                        </div>

                        @if ($matchingPeople->isNotEmpty() && ! $selectedPerson)
                            <div class="list-group list-group-flush">
                                @foreach ($matchingPeople as $person)
                                    <div class="list-group-item px-0" wire:key="person-match-{{ $person->id }}">
                                        <div class="d-flex flex-column flex-lg-row align-items-start gap-3">
                                            <img
                                                    src="{{ $person->portrait_url->thumbUrl() }}"
                                                    alt="{{ $person->name }}"
                                                    class="img-thumbnail rounded-circle"
                                                    style="width: 56px; height: 56px; object-fit: cover;"
                                            >
                                            <div class="flex-grow-1">
                                                <div class="fw-semibold">{{ $person->name }}</div>
                                                <div class="text-muted small">{{ $person->email ?: __('features.substitutes.pool.create.matches.no_email') }}</div>
                                                <div class="d-flex flex-wrap gap-1 mt-2">
                                                    @if ($person->hasRole(\App\Models\Utilities\SchoolRoles::$SUBSTITUTE))
                                                        <span class="badge text-bg-primary">{{ trans_choice('features.substitutes', 1) }}</span>
                                                    @endif
                                                    @if ($person->hasRole(\App\Models\Utilities\SchoolRoles::$OLD_SUBSTITUTE))
                                                        <span class="badge text-bg-warning">{{ __('features.substitutes.pool.create.badges.old_substitute') }}</span>
                                                    @endif
                                                    @if ($person->trashed())
                                                        <span class="badge text-bg-secondary">{{ __('features.substitutes.pool.create.badges.soft_deleted') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div>
                                                <button type="button" class="btn btn-outline-primary btn-sm"
                                                        wire:click="selectExistingPerson({{ $person->id }})">
                                                    {{ __('features.substitutes.pool.create.matches.use_existing') }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="form-check mt-3">
                                <input
                                        id="confirm-new-person"
                                        class="form-check-input @error('confirmCreateNewPerson') is-invalid @enderror"
                                        type="checkbox"
                                        wire:model="confirmCreateNewPerson"
                                >
                                <label for="confirm-new-person" class="form-check-label">
                                    {{ __('features.substitutes.pool.create.matches.confirm_new') }}
                                </label>
                                @error('confirmCreateNewPerson')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        @elseif ($selectedPerson)
                            <div class="alert alert-success mb-0" role="alert">
                                {{ __('features.substitutes.pool.create.matches.selected_help') }}
                            </div>
                        @else
                            <div class="text-muted small">{{ __('features.substitutes.pool.create.matches.empty') }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                    <div>
                        <h2 class="h5 mb-1">{{ __('features.substitutes.pool.create.profile') }}</h2>
                        <p class="text-muted small mb-0">{{ __('features.substitutes.pool.create.profile.description') }}</p>
                    </div>
                </div>

                <div class="border rounded-3 p-3">
                    <div class="row g-3">
                        @forelse ($campuses as $campus)
                            <div class="col-12 col-md-6" wire:key="campus-{{ $campus->id }}">
                                <div class="form-check form-switch m-0">
                                    <input
                                            class="form-check-input"
                                            type="checkbox"
                                            role="switch"
                                            id="campus-{{ $campus->id }}"
                                            value="{{ $campus->id }}"
                                            wire:model="campusIds"
                                    >
                                    <label class="form-check-label"
                                           for="campus-{{ $campus->id }}">{{ $campus->name }}</label>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-muted">{{ __('features.substitutes.pool.create.profile.no_campuses') }}</div>
                        @endforelse
                    </div>
                </div>
                @error('campusIds')
                <div class="text-danger small mt-2">{{ $message }}</div>
                @enderror
                @error('campusIds.*')
                <div class="text-danger small mt-2">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="{{ route('features.substitutes.pool.index') }}" class="btn btn-outline-secondary">{{ __('common.cancel') }}</a>
            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                {{ $selectedPerson ? __('features.substitutes.pool.create.actions.import') : __('features.substitutes.pool.create.actions.add') }}
            </button>
        </div>
    </form>
</div>
