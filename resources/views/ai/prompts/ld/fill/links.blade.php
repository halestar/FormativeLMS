<p>The following Links are suggested for this Learning Demonstration:</p>
<ul class="list-group">
    @foreach($links as $link)
        <li class="list-group-item">
            <a href="{{ $link['url'] }}" target="_new">{{ $link['title'] }}</a>
        </li>
    @endforeach
</ul>
<p class="my-3">The prompt returned the following text:</p>
<pre class="text-wrap mb-3">
    {!! nl2br($prompt->last_results) !!}
</pre>