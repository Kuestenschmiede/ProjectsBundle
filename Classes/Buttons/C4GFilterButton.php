<?php

namespace con4gis\ProjectsBundle\Classes\Buttons;

class C4GFilterButton
{
    protected $icon;
    protected $description;
    protected $class;

    public function __construct(string $icon, string $description, string $class)
    {
        $this->icon = $icon;
        $this->description = $description;
        $this->class = $class;
    }

    /**
     * @return string
     */
    public function getButtonHtml(): string
    {
        return '<span class="ui-button ui-corner-all" title="'.$this->description.
            '" onclick="let element = document.getElementsByClassName(\'c4g_brick_list\')[0];'.
            'element.classList.toggle(\'filter_'.$this->class.'_parent\');'.
            'this.classList.toggle(\'c4g_button_pressed\')">'.$this->icon.'</span>';
    }
}
