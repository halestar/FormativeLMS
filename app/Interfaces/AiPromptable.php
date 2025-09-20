<?php

namespace App\Interfaces;

use App\Models\Ai\AiPrompt;
use App\Models\People\Person;
use Prism\Prism\Schema\ObjectSchema;

interface AiPromptable
{
	/**
	 * @return string The name of the model that the AI Prompt is for.
	 */
	public function getEditableName(): string;
	
	/**
	 * @return array <string, string> The breadcrumb for how to reach this model. Used in the editor.
	 */
	public function getBreacrumb(): array;
	
	/**
	 * This function will return the description of the prompt for this model.
	 * It should be specific to the object, not the information that it fills.
	 * For example, for the KnowledgeSkill model, it should say "Generate Knowledge Skill Rubric",
	 * since the AI will be used for the rubric and not the skill, but both should be referenced.
	 * @return string The description of the prompt for this model.
	 */
	public static function promptDescription(): string;
	
	/**
	 * This function will return the default prompt for this model. The default prompt will
	 * ALWAYS be the system prompt, meaning that the person_id in the prompt column will be
	 * set to null. If the prompt is not found, it will create a new prompt and return it.
	 * @param bool $overwrite Default false. If set to true, it will overwrite the changes made to
	 * the default prompt using the value at creation time.
	 * @return AiPrompt The system prompt for this model.
	 */
	public function getDefaultPrompt(bool $overwrite = false): AiPrompt;
	
	/**
	 * This function is used to determine if the person has a prompt for this model.
	 * @param Person $person The person to check for a prompt.
	 * @return bool True if the person has a prompt for this model.
	 */
	public function hasCustomPrompt(Person $person): bool;
	
	/**
	 * Returns the prompt for this model for the given person. If the person does not have a
	 * prompt, it will create one and return it using the default prompt.
	 * @param Person $person The person to get the prompt for.
	 * @return AiPrompt The prompt for this model for the given person.
	 */
	public function customPrompt(Person $person): AiPrompt;
	
	/**
	 * @return ObjectSchema The schema for this model that AI uses to generate structured responses.  We don't save
	 * this in the DB since it might change over time.
	 */
	public function getAiSchema(): ObjectSchema;
	
	/**
	 * This function will return a mockup of the results that the AI prompt will generate.  WHat
	 * the mockup looks like it's up to you. It can be a table, or a full page.
	 * @param AiPrompt $prompt The prompt that was executed, with the latest results stored in AiPrompt::last_results
	 * @return string An HTML string that represents the mockup of the AI results.
	 */
	public function fillMockup(AiPrompt $prompt): string;
	
	/**
	 * This function will fill the model with the structured result from AI. It will NOT save the model.
	 * @param AiPrompt $prompt TThe prompt that was executed, with the latest results stored in AiPrompt::last_results
	 * @return void
	 */
	public function aiFill(AiPrompt $prompt): void;
}
