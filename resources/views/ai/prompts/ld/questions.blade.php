<p>I am writing a Learning Demonstration with the name {!! $demonstration_name !!} and abbreviation {!! $demonstration_abbr !!}</p>
<p>
    I would like you to provide me 2-5 Leading Questions that will help students synthesize what they've demonstrated
    in the Learning Demonstration.
</p>
<p>The Learning Demonstration will be posted to the "{!! $demonstration_course !!}" classes</p>
<p>The Learning Demonstration has the following skills attached: {!! $demonstration_skills !!}</p>
<p>
    You can get the information about the Learning Demonstration by looking it up using the "Get Learning Demonstration" Tool
    and the Learning Demonstration ID "{!! $demonstration_id !!}"
</p>
<p>
    I would like you to suggest 2-5 "Leading Questions", which are questions that the student is expected
    to answer either during or after the Learning Demonstration is completed.  The purpose of these questions is
    to provide a way for the student to reflect on the work that have done.  The questions should be written in a way that
    is clear, concise, and easy to understand. The questions should be written in a way that encourages the student
    to think critically and reflect on their work. The questions can be:
</p>
<ul>
    <li>Short Answer (TYPE 1): The student is expected to write a short answer, maybe 1-2 sentences.</li>
    <li>Long Answer (TYPE 2): The student is expected to write a longer answer, maybe 1-3 paragraphs.</li>
    <li>Multiple Choice (TYPE 3): The student is expected to choose one of the provided options. The options are stored in an array of options.</li>
    <li>True/False (TYPE 4): The student is expected to answer either true or false.</li>
    <li>Multiple Choices (TYPE 5): The student is expected to choose one or more of the provided options. The options are stored in an array of options.</li>
</ul>
<div class="mceNonEditable">
    <p>Please provide your response in the following format:</p>
    QUESTION: [question to ask], TYPE: [type of question], OPTIONS: [array of options, in case of type 3 or 5]
</div>