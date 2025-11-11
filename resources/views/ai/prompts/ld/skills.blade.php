<p>I am writing a Learning Demonstration with the name {!! $demonstration_name !!} and abbreviation {!! $demonstration_abbr !!} and the following description:</p>
<p>{!! $demonstration_description !!}</p>
<p>
    I would like you to provide me a list of Skills that I should use to assess this Learning Demonstration. The skills are
    all defined by the school and can be searched using the Search Skills Tool. Use one or two words to search for the skill.
    Only suggest 2-4 skills.
</p>
<p>The Learning Demonstration will be posted to the "{!! $demonstration_course !!}" classes</p>
<p>
    You can get the information about the Learning Demonstration by looking it up using the "Get Learning Demonstration" Tool
    and the Learning Demonstration ID "{!! $demonstration_id !!}"
</p>
<p>
    Please provide me with a list of skills that I should use to assess this Learning Demonstration.  Provide me with the
    skill name or designation (or both), the skill id (which will be an unsigned integer) and a reason why you
    believe this skill is appropriate for this Learning Demonstration.  You can use the "Search Skills" tool to get skills
    through a keyword search and you can ues the tool "Get Skill Information" to get more information about a specific skill via its id.
</p>
<div class="mceNonEditable">
    <p>
        For each skill suggestion, please format it in a single line like this:
    </p>
    SKILL: [skill name or designation], ID: [skill id], REASON: [reason why this skill is appropriate for this Learning Demonstration]
</div>
