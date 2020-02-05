<?php

namespace con4gis\ProjectsBundle\Classes\Buttons;

class C4GSelectFilterButton implements C4GFilterButtonInterface
{
    protected $options;

    public function __construct(array $options)
    {
        $this->options = $options;
    }

    /**
     * @return string
     */
    public function getButtonHtml(): string
    {
        $options = '<option value="">-</option>';
        foreach ($this->options as $key => $option) {
            $options .= "<option value=\"$option\">$option</option>";
        }

        $classesToRemove = '';
        foreach ($this->options as $key => $option) {
            $classesToRemove .= "element.classList.remove('filter_type_" . str_replace(' ', '', $option) . "\_parent');";
        }

        return '<select class="c4g_list_filter" onchange="let element = document.getElementsByClassName(\'c4g_brick_list\');' .
            'element = element.item(element.length - 1);' . $classesToRemove .
            'element.classList.add(\'filter_type_\' + this.options[this.selectedIndex].value.replace(/\s+/g, \'\') + \'_parent\');">' . $options .
            '</select>';
    }
}
