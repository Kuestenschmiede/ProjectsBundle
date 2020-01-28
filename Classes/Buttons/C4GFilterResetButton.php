<?php

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
        return '<span class="ui-button ui-corner-all" title="' . $this->buttonDescription .
            '" onclick="let element = document.getElementsByClassName(\'c4g_brick_list\');' .
            'element = element.item(element.length - 1);' .
            'element = element.parentNode.parentNode.firstChild;' .
            'let filters = element.getElementsByClassName(\'c4g_list_filter\');' .
            'Array.from(filters).forEach(function(element, index, array)'.
            '{if (element.classList.contains(\'c4g_button_pressed\')) {element.click();}'.
            'else if (element.tagName === \'INPUT\') {element.value = \'\'; jQuery(element).trigger(\'input\')}'.
            '});' .
            '">' . $this->buttonText . '</span>';
    }
}
