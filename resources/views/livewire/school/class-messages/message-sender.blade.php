<div class="text-muted d-flex justify-content-start align-items-center pe-3 pt-3 mt-2" style="height: 75px; max-height: 75px;">
    <img src="{{ $self->thumbnail_url }}"
         alt="{{ $self->name }}" class="me-3 rounded-circle avatar-list-item avatar-img-large">
    <input type="text" class="form-control form-control-lg"
           placeholder="{{ __('subjects.school.message.type') }}" wire:model="newMsg"
           wire:keydown.enter="sendMessage()"/>
    <a class="ms-3" href="#" wire:click="sendMessage()"><i class="fas fa-paper-plane fs-5"></i></a>
</div>
