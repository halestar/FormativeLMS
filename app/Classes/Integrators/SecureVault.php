<?php

namespace App\Classes\Integrators;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SecureVault
{
	private string $disk;
	private string $basePath;
	public function __construct()
	{
		$this->disk = config('lms.vault.disk');
		$this->basePath = config('lms.vault.path');
	}
	
	protected function vaultFileName(string $nameSpace): string
	{
		return $this->basePath . "/" . $nameSpace . ".json";
	}
	
	protected function getNamespaceData(string $namespace): array
	{
		//does the namespace exists?
		if(Storage::disk($this->disk)->exists($this->vaultFileName($namespace)))
			return json_decode(Storage::disk($this->disk)->get($this->vaultFileName($namespace)), true);
		return [];
	}
	
	protected function storeNamespaceData(string $namespace, array $data): void
	{
		Storage::disk($this->disk)->put($this->vaultFileName($namespace), json_encode($data));
	}
	public function store(string $nameSpace, string $key, mixed $value): void
	{
		$data = $this->getNamespaceData($nameSpace);
		$data[$key] = $value;
		$this->storeNamespaceData($nameSpace, $data);
	}
	
	public function retrieve(string $nameSpace, string $key): mixed
	{
		$data = $this->getNamespaceData($nameSpace);
		return $data[$key] ?? null;
	}
	
	public function hasKey(string $nameSpace, string $key): bool
	{
		$data = $this->getNamespaceData($nameSpace);
		return isset($data[$key]);
	}
	
	public function storeFile(UploadedFile $file, string $nameSpace, string $key): void
	{
		Log::info('storing file ' . $file->getClientOriginalName() . ' to ' . $this->basePath . "/" . $nameSpace . "_" . $key);
		Storage::disk($this->disk)->putFileAs($this->basePath, $file, $nameSpace . "_" . $key);
	}
	
	public function retrieveFilePath(string $nameSpace, string $key): string
	{
		return Storage::disk($this->disk)->path($this->basePath . "/" . $nameSpace . "_" . $key);
	}
	
	public function retrieveFile(string $nameSpace, string $key): string
	{
		return Storage::disk($this->disk)->get($this->basePath . "/" . $nameSpace . "_" . $key);
	}
	
	public function hasFile(string $nameSpace, string $key): bool
	{
		return Storage::disk($this->disk)->exists($this->basePath . "/" . $nameSpace . "_" . $key);
	}
}