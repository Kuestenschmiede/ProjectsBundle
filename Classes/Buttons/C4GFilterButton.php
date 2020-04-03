<?php

namespace con4gis\ProjectsBundle\Classes\Buttons;

class C4GFilterButton implements C4GFilterButtonInterface
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
        return '<span class="c4g_list_filter ui-button ui-corner-all ' . $this->class . '" title="' . $this->description .
            '" onclick="let element = document.getElementsByClassName(\'c4g_brick_list\');' .
            'element = element.item(element.length - 1);' .
            'element.classList.toggle(\'filter_' . $this->class . '_parent\');' .
            'this.classList.toggle(\'c4g_button_pressed\')">' . $this->icon . '</span>';
    }
}
