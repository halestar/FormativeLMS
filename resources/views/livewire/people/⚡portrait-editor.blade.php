<?php

use App\Classes\Settings\StorageSettings;
use App\Classes\Storage\DocumentFile;
use App\Enums\WorkStoragesInstances;
use App\Models\People\Person;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

new class extends Component
{
	use WithFileUploads;

	public Person $person;
	#[Locked]
	public bool $canEdit = false;

	public function mount(Person $person)
	{
		$this->person = $person;
		$user = auth()->user();
		$this->canEdit =
			$user->can('people.edit') || ($user->can('substitute.admin') && $this->person->isSubstitute()) ||
			($user->id == $this->person->id && $this->person->canEditOwnField('portrait'));
	}

	public function removePortrait()
	{
		if ($this->canEdit)
		{
			$this->person->portrait_url->remove();
			$this->person->save();
		}
	}

	#[On('document-storage-browser-files-selected')]
	public function updatePortrait($selected_items, $cb_instance)
	{
		if ($cb_instance == "profile-img" && $this->canEdit)
		{
			$storageSettings = app()->make(StorageSettings::class);
			// we should have the document file, either as the root, or the first element.
			$portrait = $selected_items;
			if (isset($portrait['school_id']))
				$doc = DocumentFile::hydrate($portrait);
			else
				$doc = DocumentFile::hydrate($portrait[0]);
			// first, we persist the file, using the Person object as the filer.
			$connection = $storageSettings->getWorkConnection(WorkStoragesInstances::ProfileWork);
			$imgFile = $connection->persistFile($this->person, $doc);
			if ($imgFile)
			{
				$this->person->portrait_url->useWorkfile($imgFile);
				$this->person->save();
			}
		}
	}
};
?>

<div class="profile-img">
    <img
            class="img-fluid img-thumbnail"
            src="{{ $person->portrait_url }}"
            alt="{{ __('people.profile.image') }}"
    />
    @if($canEdit)
        <button
                type="button"
                class="file btn btn-lg btn-dark"
                @click="$dispatch('document-storage-browser.open-browser',
                            {
                                config:
                                    {
                                        multiple: false,
                                        mimetypes: {{ Js::from(\App\Models\Utilities\MimeType::imageMimeTypes()) }},
                                        allowUpload: true,
                                        canSelectFolders: false,
                                        cb_instance: 'profile-img'
                                    }
                            });"
        >
            {{ __('people.profile.image.update') }}
        </button>
        @if($person->hasPortrait())
            <button
                    class="remove btn btn-lg btn-danger"
                    wire:click="removePortrait"
                    wire:confirm="{{ __('people.profile.image.remove.confirm') }}"
            >
                {{ __('people.profile.image.remove') }}
            </button>
        @endif
    @endif
</div>