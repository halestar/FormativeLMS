<?php

namespace App\Livewire\Storage;

use App\Models\Utilities\MimeType;
use App\Rules\IsValidHtml;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Livewire\Component;

class MimeConfiguration extends Component
{
	public Collection $mimeTypes;
	public string $mimeType = "";
	public string $ext = "";
	public string $icon = "";
	public bool $isImg = false;
	public bool $isVideo = false;
	public bool $isAudio = false;
	public bool $isDoc = true;
	public ?string $selectedMimeType = null;
	
	public function mount()
	{
		$this->mimeTypes = MimeType::all();
	}
	
	public function add()
	{
		$this->validate();
		$mime = new MimeType();
		$mime->mime = $this->mimeType;
		$mime->extension = $this->ext;
		$mime->icon = $this->icon;
		$mime->is_img = $this->isImg;
		$mime->is_video = $this->isVideo;
		$mime->is_audio = $this->isAudio;
		$mime->is_document = $this->isDoc;
		$mime->save();
		$this->clear();
		$this->mimeTypes = MimeType::all();
	}
	
	public function clear()
	{
		$this->mimeType = "";
		$this->ext = "";
		$this->icon = "";
		$this->isImg = false;
		$this->isVideo = false;
		$this->isAudio = false;
		$this->isDoc = false;
		$this->selectedMimeType = null;
	}
	
	public function setSelected(MimeType $mimeType)
	{
		$this->selectedMimeType = $mimeType->mime;
		$this->mimeType = $mimeType->mime;
		$this->ext = $mimeType->extension;
		$this->icon = $mimeType->icon;
		$this->isImg = $mimeType->is_img;
		$this->isVideo = $mimeType->is_video;
		$this->isAudio = $mimeType->is_audio;
		$this->isDoc = $mimeType->is_document;
	}
	
	public function update()
	{
		$this->validate();
		$mime = MimeType::findOrFail($this->selectedMimeType);
		$mime->mime = $this->mimeType;
		$mime->extension = $this->ext;
		$mime->icon = $this->icon;
		$mime->is_img = $this->isImg;
		$mime->is_video = $this->isVideo;
		$mime->is_audio = $this->isAudio;
		$mime->is_document = $this->isDoc;
		$mime->save();
		$this->clear();
		$this->mimeTypes = MimeType::all();
	}
	
	public function delete()
	{
		$mime = MimeType::findOrFail($this->selectedMimeType);
		if($mime)
			$mime->delete();
		$this->clear();
		$this->mimeTypes = MimeType::all();
	}
	
	public function render()
	{
		return view('livewire.storage.mime-configuration');
	}
	
	protected function rules()
	{
		if($this->selectedMimeType)
		{
			$mimeType =
				[
					'required',
					'regex:/^[a-z0-9\-]+\/[a-z0-9\-\+]+$/i',
					Rule::unique('mime_types', 'mime')
					    ->ignore($this->selectedMimeType, 'mime')
				];
		}
		else
		{
			$mimeType =
				[
					'required',
					'regex:/^[a-z0-9\-]+\/[a-z0-9\-\+]+$/i',
					Rule::unique('mime_types', 'mime'),
				];
		}
		return
			[
				'mimeType' => $mimeType,
				'ext' => 'required|regex:/^(?:\.[a-zA-Z0-9]{1,4}(?:\s*,\s*){0,1})+$/i',
				'icon' => ['required', new IsValidHtml],
				'isImg' => 'boolean',
			];
	}
}
