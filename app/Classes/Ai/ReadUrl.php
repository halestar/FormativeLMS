<?php

namespace App\Classes\Ai;

use Illuminate\Support\Facades\Http;
use Prism\Prism\Tool;

class ReadUrl extends Tool
{
	public function __construct()
	{
		$this->as('read-url')
		     ->for("useful when you need to see the content of a URL or weblink")
		     ->withStringParameter('url', "The URl what you would like to access")
		     ->using($this);
	}
	
	public function __invoke(string $url): string
	{
		$response = Http::get($url);
		if($response->successful())
			return "Here is the contents of the URL: " . $response->body();
		else
			return "An error was returned: " . $response->status();
	}
}