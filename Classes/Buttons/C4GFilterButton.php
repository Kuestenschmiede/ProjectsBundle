<?php


namespace con4gis\ProjectsBundle\Classes\Buttons;


class C4GFilterButton
{
    protected $icon;

    public function __construct(string $icon)
    {
        $this->icon = $icon;
    }

    /**
     * @return string
     */
    public function getButtonHtml(): string
    {
        return '<span class="ui-button ui-corner-all">'.$this->icon.'</span>';
    }
}