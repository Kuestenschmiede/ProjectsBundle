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

class C4GCheckboxFilterButton implements C4GFilterButtonInterface
{
    protected $label;
    protected $labelChecked = '';
    protected $labelUnChecked = '';
    protected $description;
    protected $class;
    protected $style = 'checkbox';

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
        switch ($this->style) {
            case 'checkbox':
                return '<span class="c4g_list_filter c4g_list_filter_checkbox" title="' . $this->description .
                    '" onclick="let element = document.getElementsByClassName(\'c4g_brick_list\');' .
                    'element = element.item(element.length - 1);' .
                    'element.classList.toggle(\'filter_' . $this->class . '_parent\');' .
                    'this.classList.toggle(\'checkbox_checked\')">' . $this->label . '</span>';

                break;
            case 'button':
                return '<span class="c4g_list_filter ui-button ui-corner-all" title="' . $this->description .
                    '" onclick="let element = document.getElementsByClassName(\'c4g_brick_list\');' .
                    'element = element.item(element.length - 1);' .
                    'element.classList.toggle(\'filter_' . $this->class . '_parent\');' .
                    'this.classList.toggle(\'c4g_button_pressed\');' .
                    'if (this.innerHTML === \'' . $this->labelUnChecked . '\') {this.innerHTML = \'' . $this->labelChecked . '\'}' .
                    ' else {this.innerHTML = \'' . $this->labelUnChecked . '\'}">' . $this->labelUnChecked . '</span>';

                break;
            default:
                return '';

                break;
        }
    }

    /**
     * @return string
     */
    public function getLabelChecked(): string
    {
        return $this->labelChecked;
    }

    /**
     * @param string $labelChecked
     * @return C4GCheckboxFilterButton
     */
    public function setLabelChecked(string $labelChecked): C4GCheckboxFilterButton
    {
        $this->labelChecked = $labelChecked;

        return $this;
    }

    /**
     * @return string
     */
    public function getLabelUnChecked(): string
    {
        return $this->labelUnChecked;
    }

    /**
     * @param string $labelUnChecked
     * @return C4GCheckboxFilterButton
     */
    public function setLabelUnChecked(string $labelUnChecked): C4GCheckboxFilterButton
    {
        $this->labelUnChecked = $labelUnChecked;

        return $this;
    }

    /**
     * @return string
     */
    public function getStyle(): string
    {
        return $this->style;
    }

    /**
     * @param string $style
     * @return C4GCheckboxFilterButton
     */
    public function setStyle(string $style): C4GCheckboxFilterButton
    {
        $this->style = $style;

        return $this;
    }
}
