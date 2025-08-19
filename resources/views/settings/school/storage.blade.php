@inject('storageSettings','App\Classes\Settings\StorageSettings')
<div class="row">
    <div class="col">
        <h3>{{ __('settings.storage.documents') }}</h3>
        <div class="mb-3">
            <livewire:storage.document-storage-assigner :title="__('settings.storage.documents.employee')" updated-property="employee_documents" />
        </div>
        <div class="mb-3">
            <livewire:storage.document-storage-assigner :title="__('settings.storage.documents.student')" updated-property="student_documents" />
        </div>
    </div>
    <div class="col">
        <h3>{{ __('settings.storage.work') }}</h3>
        <div class="mb-3">
            <livewire:storage.work-storage-assigner :title="__('settings.storage.work.employee')" updated-property="employee_work" />
        </div>
        <div class="mb-3">
            <livewire:storage.work-storage-assigner :title="__('settings.storage.work.student')" updated-property="student_work" />
        </div>
        <div class="mb-3">
            <livewire:storage.work-storage-assigner :title="__('settings.storage.work.class')" updated-property="class_work" />
        </div>
        <div class="mb-3">
            <livewire:storage.work-storage-assigner :title="__('settings.storage.work.email')" updated-property="email_work" />
        </div>
    </div>
</div>