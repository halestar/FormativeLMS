<div class="{{ $classes }}" style="{{ $style }}">
    <div class="d-flex justify-content-between align-items-center border-bottom mb-3">
        <h3>
            @if($classSession->viewingAs(\App\Enums\ClassViewer::FACULTY) || $classSession->viewingAs(\App\Enums\ClassViewer::ADMIN))
                {{ trans_choice('learning.demonstrations', 2) }}
            @else
                {{ trans_choice('learning.demonstrations.opportunities', 2) }}
            @endif
        </h3>
        @if($canManage)
            <div class="text-end">
                <a
                    class="btn btn-primary btn-sm"
                    role="button"
                    href="{{ route('learning.ld.create', ['course' => $classSession->course->id]) }}"
                    wire:navigate
                >
                    <i class="fa-solid fa-plus"></i>
                    <span class="border-start px-2">
                        {{ __('learning.demonstrations.new') }}
                    </span>
                </a>
                <a
                    class="btn btn-info btn-sm"
                    role="button"
                    href="{{ route('learning.ld.index', ['course' => $classSession->course->id]) }}"
                    wire:navigate
                >
                    <i class="fa-solid fa-eye"></i>
                    <span class="border-start px-2">
                        {{ trans_choice('learning.demonstrations.templates', 2) }}
                    </span>
                </a>
            </div>
        @endif
    </div>
    <ul class="list-group">
        @if($classSession->viewingAs(\App\Enums\ClassViewer::FACULTY))
            @foreach($demonstrations as $demonstration)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span class="fs-6 fw-bold">
                        {{ $demonstration->name }} ({{ $demonstration->abbr }})
                    </span>
                    <div class="text-end me-2">
                        <a
                            class="text-primary me-2 fs-5 link-underline link-underline-opacity-0"
                            href="{{ route('learning.ld.edit', ['ld' => $demonstration->id, 'classSession' => $classSession->id]) }}"
                            wire:navigate
                        >
                            <i class="fa-solid fa-edit"></i>
                        </a>
                        <a
                                class="text-primary me-2 fs-5 link-underline link-underline-opacity-0"
                                href="{{ route('learning.ld.assess', ['ld' => $demonstration->id, 'classSession' => $classSession->id]) }}"
                                wire:navigate
                        >
                            <i class="fa-solid fa-user-graduate"></i>
                        </a>
                    </div>
                </li>
            @endforeach
        @elseif($classSession->viewingAs(\App\Enums\ClassViewer::STUDENT))
            @foreach($opportunities as $opportunity)
                <li class="list-group-item">
                    {{ $opportunity->demonstration->name }} ({{ $opportunity->demonstration->abbr }})
                </li>
            @endforeach
        @endif
    </ul>
</div>
