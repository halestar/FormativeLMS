<?php

namespace App\Livewire\School;

use App\Classes\ClassManagement\ClassLinksWidget;
use App\Models\SubjectMatter\ClassSession;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ClassLinks extends Component
{
    public ClassLinksWidget $widget;
    public ClassSession $session;
    public string $classLinksTitle;
    public bool $canManage = false;
    public bool $adding = false;
    public bool $editing = false;
    public array $links = [];
    public array $otherWidgets = [];
    public array $sessionWidgets = [];
    public ?string $editId = null;
    #[Validate('required|min:3')]
    public string $linkText = '';
    #[Validate('required|url')]
    public string $linkUrl = '';
    public array $alsoPost = [];
    public bool $notify = false;

    public function mount(ClassLinksWidget $classWidget, bool $canManage = false)
    {
        $this->widget = $classWidget;
        $this->canManage = $canManage;
        $this->links = $this->widget->getLinks();
        $this->classLinksTitle = $this->widget->title;
        //get all the other widgets that we can posts announcement to
        $sessions = ClassLinksWidget::sessionsWithWidgets();
        $this->sessionWidgets = [];
        $this->otherWidgets = [];
        foreach ($sessions as $session)
        {
            $sessionWidgets = [];
            foreach($session->layout->getWidgetTypes(ClassLinksWidget::class) as $widget)
            {
                if($widget->getId() != $this->widget->getId())
                {
                    $sessionWidgets[$widget->getId()] = $widget;
                    $this->otherWidgets[$widget->getId()] = $widget;
                }
            }
            if(count($sessionWidgets) > 0)
            {
                $this->sessionWidgets[] =
                    [
                        'session' => $session,
                        'widgets' => $sessionWidgets,
                    ];
            }
        }
        $this->session = $this->widget->getClassSession();
    }

    public function updateTitle()
    {
        $this->widget->title = $this->classLinksTitle;
        $this->widget->saveWidget();
    }

    public function setAdd()
    {
        $this->adding = true;
    }

    public function setEdit(string $linkId)
    {
        $this->editing = true;
        $this->adding = false;
        $this->editId = $linkId;
        $link = $this->widget->getLink($linkId);
        $this->linkText = $link['text'];
        $this->linkUrl = $link['url'];
    }

    public function addLink():void
    {
        $this->validate();
        $linkData =
            [
                'text' => $this->linkText,
                'url' => $this->linkUrl,
            ];
        $this->widget->addLink($linkData);
        foreach($this->alsoPost as $widgetId)
            $this->otherWidgets[$widgetId]->addLink($linkData);
        $this->clearLinkForm();
        $this->links = $this->widget->getLinks();
    }

    public function updateLink()
    {
        $this->validate();
        $linkData =
            [
                'id' => $this->editId,
                'text' => $this->linkText,
                'url' => $this->linkUrl,
            ];
        $this->widget->updateLink($linkData, $this->notify);
        $this->clearLinkForm();
        $this->links = $this->widget->getLinks();
    }

    public function clearLinkForm()
    {
        $this->adding = false;
        $this->editing = false;
        $this->linkText = '';
        $this->linkUrl = '';
        $this->editId = null;
    }

    public function deleteLink(string $linkId)
    {
        $this->widget->removeLink($linkId);
        $this->links = $this->widget->getLinks();
    }

    public function render()
    {
        return view('livewire.school.class-links');
    }
}
