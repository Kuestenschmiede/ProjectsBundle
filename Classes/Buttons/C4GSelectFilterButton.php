<?php
/**
 * This file is part of con4gis,
 * the gis-kit for Contao CMS.
 *
 * @package    con4gis
 * @version    7
 * @author     con4gis contributors (see "authors.txt")
 * @license    LGPL-3.0-or-later
 * @copyright  Küstenschmiede GmbH Software & Design
 * @link       https://www.con4gis.org
 */
namespace con4gis\ProjectsBundle\Classes\Buttons;

class C4GSelectFilterButton implements C4GFilterButtonInterface
{
    protected $options;
    protected $class;
    protected $label;
    protected $id;
    protected $labelMode;

    public function __construct(array $options, string $class, string $label, string $id, int $labelMode)
    {
        $this->options = $options;
        $this->class = $class;
        $this->label = $label;
        $this->id = $id;
        $this->labelMode = $labelMode;
    }

    /**
     * @return string
     */
    public function getButtonHtml(): string
    {
        if ($this->labelMode === 1 || $this->labelMode === 2) {
            $options = '<option value="">' . $this->label . '</option>';
        } else {
            $options = '<option value="">-</option>';
        }

        foreach ($this->options as $key => $option) {
            $options .= '<option value="' . str_replace([' ', '/', '.', ',', '-', '&'], '', $option) . "\">$option</option>";
        }

        $classesToRemove = '';
        foreach ($this->options as $key => $option) {
            $classesToRemove .= "element.classList.remove('filter_" . $this->class . '_' . str_replace([' ', '/', '.', ',', '-', '&'], '', $option) . "\_parent');";
        }

        if ($this->labelMode === 0 || $this->labelMode === 2) {
            $label = '<label for="' . $this->id . '">' . $this->label . '</label>';
            $ariaLabel = '';
        } else {
            $label = '<label for="' . $this->id . '"></label>';
            $ariaLabel = ' aria-label="' . $this->label . '"';
        }

        return '<span>' . $label . '</span><span><select id="' . $this->id . '" class="c4g_list_filter" onchange="let element = document.getElementsByClassName(\'c4g_brick_list\');' .
            'element = element.item(element.length - 1);' . $classesToRemove .
            'element.classList.add(\'filter_' . $this->class . '_\' + this.options[this.selectedIndex].value.replace(/\s+/g, \'\') + \'_parent\');"' . $ariaLabel . '>' . $options .
            '</select></span>';
    }
}
