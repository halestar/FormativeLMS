<?php

return [
    'component' => \App\Livewire\School\ClassPage::class,
    'widgets' =>
    [
        \App\Classes\ClassManagement\ClassAnnouncementsWidget::class,
        \App\Classes\ClassManagement\ClassLinksWidget::class,
    ],
];
