<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CleanLang extends Command
{
	public static array $languages =
		[
			'en' => 'English',
			'es' => 'Spanish',
		];
    /**r
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fablms:clean-lang {base_language : The language to use as the base for comparison. Defaults to en}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will go through all the lang files and make sure that all keys are synced across the different languages.  Missing keys will be added.';

	private function writeLangFile(string $langFile, array $translations)
	{
		ksort($translations);
		$contents = "<?php\n\nreturn\n[\n";
		foreach($translations as $key => $value)
		{
			//escape value
			$eValue = str_replace(["'", '$'], ["\\'", '\$'], $value);
			//special case for multilines
			if(str_contains($value, "\n"))
				$contents .= "\t'" . $key . "' => <<<EOE\n" . $eValue . "\nEOE,\n";
			else
				$contents .= "\t'" . $key . "' => '" . $eValue . "',\n";
		}
		$contents .= '];';
		File::put($langFile, $contents);
		$this->info("Wrote translations to " . $langFile);
	}
    /**
     * Execute the console command.
     */
    public function handle()
    {
		$baseLanguage = $this->argument('base_language')?? 'en';
        $langs = File::directories(base_path('lang'));
		//next, we get the base language files.
	    $langFiles = File::files(base_path('lang/' . $baseLanguage));
		//we will iterate through each of the language files in the base language.
	    foreach($langFiles as $langFile)
	    {
			//first,we read in the file in as an array.
		    $translations = include $langFile->getPathname();
			//next we iterate through each of the languages.
		    $this->writeLangFile($langFile->getPathname(), $translations);
		    
		    //since we will be adding keys in the orginal language, we will preface it with the language name
		    $modTranslations = [];
			foreach($translations as $key => $value)
				$modTranslations[$key] = self::$languages[$baseLanguage] . ": " . $value;
		    foreach($langs as $lang)
		    {
				//if it's the base, we can skip
			    if($lang == base_path('lang/' . $baseLanguage)) continue;
				//first off, does this file exists?
			    if(!File::exists($lang . '/' . $langFile->getFilename()))
			    {
					$this->info($langFile->getFilename() . " does not exist in " . $lang . ". Creating it.");
					//it does not, so we will create it and pass it the translations from the base language
				    $newTranslations = $modTranslations;
			    }
				else
				{
					$this->info($langFile->getFilename() . " exists in " . $lang . ". Checking for missing keys.");
					//in this case we will need to compare the entries on this file to the base language file.
					$newTranslations = include $lang . '/' . $langFile->getFilename();
					foreach($modTranslations as $key => $value)
					{
						if(isset($newTranslations[$key]))
							continue;
						$this->info("Adding missing key: " . $key);
						$newTranslations[$key] = $value;
					}
				}
			    $this->writeLangFile($lang . '/' . $langFile->getFilename(), $newTranslations);
		    }
	    }
		
    }
}
