<?php

namespace con4gis\ProjectsBundle\Classes\Buttons;

class C4GCheckboxFilterButton implements C4GFilterButtonInterface
{
    protected $label;
    protected $description;
    protected $class;

    public function __construct(string $label, string $description, string $class)
    {
        $this->label = $label;
        $this->description = $description;
        $this->class = $class;
    }

    /**
     * @return string
     */
    public function getButtonHtml(): string
    {
        return '<span class="checkbox" title="' . $this->description .
            '" onclick="let element = document.getElementsByClassName(\'c4g_brick_list\');' .
            'element = element.item(element.length - 1);' .
            'element.classList.toggle(\'filter_' . $this->class . '_parent\');' .
            'this.classList.toggle(\'checkbox_checked\')">' . $this->label . '</span>';
    }
}
