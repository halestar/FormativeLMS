<?php

namespace App\Interfaces;

use App\Models\Ai\AiPrompt;
use App\Models\People\Person;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\JsonSchema\JsonSchema;
use Prism\Prism\Schema\ObjectSchema;

interface AiPromptable
{
    /**
     * This function will return the propertied that are available for AI to work with. Each property will define
     * a section of the model that AI can fill in through a prompt.  For example, the Skill model will have a single
     * property, rubric, that will allow AI to fill in the rubric property for the skill. The LearningDemonstrationTemplate,
     * on the other hand, will have multiple properties. The "description" property will allow AI to fill in the description
     * for the LD. The "skills" property will allow AI to select the appropriate assessment skill(s) and rubrics for
     * the learning demonstration to assess.
     *
     * @return array <string, string> The list of properties that AI can fill in for this model in the format of [property_name => property_label]
     */
    public static function availableProperties(): array;

    /*******************************************************************************
     * The following functions are used to define and register the AIPrompt model
     * for this model and a passed property.
     *******************************************************************************/
    /**
     * The default prompt for this model
     *
     * @param  string  $property  The property that the prompt is for.
     * @return string The default prompt for this model as a string. Can be formatted in any way.
     */
    public static function defaultPrompt(string $property): string;

    /**
     * The default system prompt for this model.
     *
     * @param  string  $property  The property that the prompt is for.
     * @return string The default system prompt for this model as a string. Can be formatted in any way.
     */
    public static function defaultSystemPrompt(string $property): string;

    /**
     * Gets the default AI temperature for this model.
     *
     * @param  string  $property  The property that the prompt is for.
     * @return float The default AI temperature for this model.
     */
    public static function defaultTemperature(string $property): float;

    /**
     * @return bool True if the property is structured, false if it's not.
     */
    public static function isStructured(string $property): bool;

    /**
     * This will eventually be used to provide a way to manage tokens if they need to be assigned to users or
     * as a total. Will have to be integrated later.
     *
     * @return array<string, string> The tokens that are available to be used in the prompt.
     */
    public static function availableTokens(string $property): array;

    /*******************************************************************************
     * The following functions are used by the integrator to interact with the model making the request
     *******************************************************************************/

    /**
     * @return JsonSchema|null The schema for this model that AI uses to generate structured responses.  We don't save
     *                         this in the DB since it might change over time. Only applies to structured models, returns null for unstructured models.
     */
    public static function getSchema(string $property): ?ObjectSchema;

    /**
     * This function will return a mockup of the results that the AI prompt will generate.  WHat
     * the mockup looks like it's up to you. It can be a table, or a full page.
     *
     * @param  AiPrompt  $prompt  The prompt that was executed, with the latest results stored in AiPrompt::last_results
     * @return string An HTML string that represents the mockup of the AI results.
     */
    public function fillMockup(AiPrompt $prompt): string;

    /**
     * This function will fill the model with the structured result from AI. It will NOT save the model.
     *
     * @param  AiPrompt  $prompt  The prompt that was executed, with the latest results stored in AiPrompt::last_results
     */
    public function aiFill(AiPrompt $prompt): void;

    /**
     * This function will return the tokens defined in the available_tokens function with the actual values
     * from the instanced object.
     *
     * @return array <string, mixed> The definition of each token with the actual value.
     */
    public function withTokens(): array;
}
