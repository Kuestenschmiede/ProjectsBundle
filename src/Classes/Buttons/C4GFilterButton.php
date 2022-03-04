<?php
/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 8
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2022, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */
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
        return '<span class="c4g_list_filter c4g__btn c4g__btn-filter ' . $this->class . '" title="' . $this->description .
            '" onclick="let element = document.getElementsByClassName(\'c4g_brick_list\');' .
            'element = element.item(element.length - 1);' .
            'element.classList.toggle(\'filter_' . $this->class . '_parent\');' .
            'this.classList.toggle(\'c4g_button_pressed\')">' . $this->icon . '</span>';
    }
}
