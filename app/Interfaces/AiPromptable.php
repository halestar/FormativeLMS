<?php

namespace App\Interfaces;

use App\Classes\AI\AiSchema;
use App\Models\Ai\AiPrompt;
use App\Models\People\Person;
use Illuminate\Database\Eloquent\Builder;
use Prism\Prism\Schema\ObjectSchema;

interface AiPromptable
{
	/*******************************************************************************
	 * The following functions are used by the system to display meta-data information about this
	 * model.
	 *******************************************************************************/
	
	/**
	 * This function will return the propertied that are available for AI to work with. Each property will define
	 * a section of the model that AI can fill in through a prompt.  For example, the Skill model will have a single
	 * property, rubric, that will allow AI to fill in the rubric property for the skill. The LearningDemonstrationTemplate,
	 * on the other hand, will have multiple properties. The "description" property will allow AI to fill in the description
	 * for the LD. The "skills" property will allow AI to select the appropriate assessment skill(s) and rubrics for
	 * the learning demonstration to assess.
	 * @return array <string, string> The list of properties that AI can fill in for this model in the format of [property_name => property_label]
	 */
	public static function availableProperties(): array;
	
	/*******************************************************************************
	 * The following functions are used to define and register the AIPrompt model
	 * for this model and a passed property.
	 *******************************************************************************/
	/**
	 * The default prompt for this model
	 * @param string $property The property that the prompt is for.
	 * @return string The default prompt for this model as a string. Can be formatted in any way.
	 */
	public static function defaultPrompt(string $property): string;

	/**
	 * The default system prompt for this model.
	 * @param string $property The property that the prompt is for.
	 * @return string The default system prompt for this model as a string. Can be formatted in any way.
	 */
	public static function defaultSystemPrompt(string $property): string;
	
	
	/**
	 * Gets the default AI temperature for this model.
	 * @param string $property The property that the prompt is for.
	 * @return float The default AI temperature for this model.
	 */
	public static function defaultTemperature(string $property): float;
	
	/**
	 * @return bool True if the property is structured, false if it's not.
	 */
	public static function isStructured(string $property): bool;
	
	/**
	 * @return array<string, string> The tokens that are available to be used in the prompt.
	 */
	public static function availableTokens(string $property): array;
	
	/*******************************************************************************
	 * The following functions are used by the integrator to interact with the model making the request
	 *******************************************************************************/

	/**
	 * @return AiSchema|null The schema for this model that AI uses to generate structured responses.  We don't save
	 * this in the DB since it might change over time. Only applies to structured models, returns null for unstructured models.
	 */
	public static function getSchemaClass(string $property): ?AiSchema;
	
	/**
	 * This function will return a mockup of the results that the AI prompt will generate.  WHat
	 * the mockup looks like it's up to you. It can be a table, or a full page.
	 * @param AiPrompt $prompt The prompt that was executed, with the latest results stored in AiPrompt::last_results
	 * @return string An HTML string that represents the mockup of the AI results.
	 */
	public function fillMockup(AiPrompt $prompt): string;
	
	/**
	 * This function will fill the model with the structured result from AI. It will NOT save the model.
	 * @param AiPrompt $prompt The prompt that was executed, with the latest results stored in AiPrompt::last_results
	 * @return void
	 */
	public function aiFill(AiPrompt $prompt): void;
	
	/**
	 * This function will return the tokens defined in the available_tokens function with the actual values
	 * from the instanced object.
	 * @return array <string, mixed> The definition of each token with the actual value.
	 */
	public function withTokens(): array;
	
	
	/*******************************************************************************
	 * Defined in App\Traits\IsAiPromptable
	 *******************************************************************************/
	
	/**
	 * This function will return the prompts that are available for this model as a Builder object.
	 * @return Builder The query builder for the prompts that are available for this model.
	 */
	public static function prompts(): Builder;

	
	/**
	 * Returns the prompt for this model for the given person. If the person does not have a
	 * prompt, it will create one and return it using the default prompt.
	 * @param Person $person The person to get the prompt for.
	 * @return AiPrompt The prompt for this model for the given person.
	 */
	public static function getUserPrompt(string $property, Person $person): AiPrompt;
	
	/**
	 * @param string $property The property that the prompt is for.
	 * @return string The "pretty" name of the property.
	 */
	public static function propertyName(string $property): string;
}
