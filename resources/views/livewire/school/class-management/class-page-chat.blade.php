<div class="card">
    <div class="card-body" style="max-height: 50vh; height: 50vh;">
        <livewire:school.class-messages.class-chat
                :session="$classSession"
                :student="$student"
                classes="flex-grow-1"
                style="height: calc(100% - 70px);"
        />
        <livewire:school.class-messages.message-sender
                :session="$classSession"
                :student="$student"
        />
    </div>
</div>
