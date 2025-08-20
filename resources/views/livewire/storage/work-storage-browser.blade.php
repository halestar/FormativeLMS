<div class="card text-bg-info">
    <h4 class="card-header">
        {{ $title }}
    </h4>
    <div class="card-body">
        <ul class="list-group list-group-flush">
            @foreach($workFiles as $file)
                <li class="list-group-item">
                    {{ $file->name }}
                </li>
            @endforeach
        </ul>
    </div>
</div>
