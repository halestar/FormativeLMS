<?php

namespace App\Mcp\Tools;

use App\Models\Utilities\WorkFile;
use Illuminate\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsIdempotent;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[IsReadOnly]
#[IsIdempotent]
class WorkFileAccessor extends Tool
{
	protected string $name = 'work-file-accessor';
	protected string $title = "Work File Accessor";
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
        This tool allows the AI to access work file documents.
    MARKDOWN;
	
	protected string $privateRoute, $publicRoute;
	
	public function __construct()
	{
		$this->privateRoute = str_replace('<file_id>', '',
			route('settings.work.file.private', ['work_file' => "<file_id>"]));
		$this->publicRoute = str_replace('<file_id>', '',
			route('settings.work.file.public', ['work_file' => "<file_id>"]));
	}

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $validated = $request->validate(
			[
				'url' => 'required|url|starts_with:' . $this->publicRoute . ',' . $this->privateRoute,
			],
            [
				'url.required' => 'The URL of the work file document is required.',
				'url.starts_with' => 'The URL of the work file document must start with ' . $this->publicRoute . ' or ' . $this->privateRoute . '.',
            ]);
		//isolate the ID from the URL
	    $id = str_replace([$this->publicRoute, $this->privateRoute], '', $validated['url']);
		$workFile = WorkFile::findOrFail($id);
		if(!$workFile)
			return Response::error('The file with URL ' . $validated['url'] . ' does not exist.');
        return Response::blob($workFile->lmsConnection->fileContents($workFile));
    }

    /**
     * Get the tool's input schema.
     *
     * @return array<string, \Illuminate\JsonSchema\JsonSchema>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'url' => $schema->string()
                ->description('The URL of the work file document.  The URl starts with ' .
	                $this->publicRoute . '<file_id> for public files or with ' .
	                $this->privateRoute . '<file_id> for private files, where <file_id> is the ID of the work file.')
                ->required(),
        ];
    }
}
