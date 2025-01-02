<?php
/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 10
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2025, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */
namespace con4gis\ProjectsBundle\Classes\Buttons;

class C4GFilterResetButton implements C4GFilterButtonInterface
{
    protected $buttonText;
    protected $buttonDescription;

    public function __construct(string $buttonText, string $buttonDescription)
    {
        $this->buttonText = $buttonText;
        $this->buttonDescription = $buttonDescription;
    }

    /**
     * @return string
     */
    public function getButtonHtml(): string
    {
        return '<span class="c4g__btn c4g__btn-filter" title="' . $this->buttonDescription .
            '" onclick="let element = document.getElementsByClassName(\'c4g_brick_list\');' .
            'element = element.item(element.length - 1);' .
            'element = element.parentNode.parentNode.firstChild;' .
            'let filters = element.getElementsByClassName(\'c4g_list_filter\');' .
            'Array.from(filters).forEach(function(element, index, array)' .
            '{if (element.classList.contains(\'c4g_button_pressed\')|| element.classList.contains(\'checkbox_checked\')) {element.click();}' .
            'else if (element.tagName === \'INPUT\') {element.value = \'\'; jQuery(element).trigger(\'input\')}' .
            'else if (element.tagName === \'SELECT\') {element.value = \'\'; jQuery(element).trigger(\'change\')}' .
            '});' .
            '">' . $this->buttonText . '</span>';
    }
}
