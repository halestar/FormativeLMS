<p>The following Leading Questions are suggested for this Learning Demonstration:</p>
<ul class="list-group">
    @foreach($questions as $question)
        <li class="list-group-item">
            <strong>Question:</strong> {!! $question['question'] !!}
            [TYPE: {{ \App\Classes\Learning\DemonstrationQuestion::typeOptions()[$question['type']] }}]
            @if($question['type'] == \App\Classes\Learning\DemonstrationQuestion::TYPE_MULTIPLE ||
                $question['type'] == \App\Classes\Learning\DemonstrationQuestion::TYPE_CHOICE)
                Choices: {!! implode(', ', $question['choices']) !!}
            @endif
        </li>
    @endforeach
</ul>
<p class="my-3">The prompt returned the following text:</p>
<pre class="text-wrap mb-3">
    {!! nl2br($prompt->last_results) !!}
</pre>