<p>The following Skills are suggested for this Learning Demonstration:</p>
<ul class="list-group">
    @foreach($skills as $skill)
        <li class="list-group-item list-group-item-action">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5>{{ $skill->prettyName() }}</h5>
                    <p><strong>{{ __('ai.prompt.ld.templates.skills.reason') }}:</strong> {{ $reasons[$skill->id] }}</p>
                </div>
                <button type="button" @click="$dispatch('skill-selector.skills-added', { skill: {{ $skill->id }} })"
                        class="ms-3 btn btn-primary text-nowrap">{{ __('subjects.skills.add') }}</button>
            </div>
        </li>
    @endforeach
</ul>
<p class="my-3">The prompt returned the following text:</p>
<pre class="text-wrap mb-3">
    {!! nl2br($prompt->last_results) !!}
</pre>