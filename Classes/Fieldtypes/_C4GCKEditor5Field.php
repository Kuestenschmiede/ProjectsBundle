<?php
/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 8
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2021, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */
namespace con4gis\ProjectsBundle\Classes\Fieldtypes;

use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialogParams;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickField;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickFieldCompare;

class C4GCKEditor5Field extends C4GBrickField
{
    protected static $instances = 0;
    protected $removedPlugins = [
        self::PLUGIN_CK_FINDER_UPLOAD_ADAPTER,
        self::PLUGIN_CK_FINDER,
        self::PLUGIN_EASY_IMAGE,
        self::PLUGIN_HEADING,
        self::PLUGIN_IMAGE,
        self::PLUGIN_IMAGE_CAPTION,
        self::PLUGIN_IMAGE_STYLE,
        self::PLUGIN_IMAGE_TOOLBAR,
        self::PLUGIN_IMAGE_UPLOAD,
        self::PLUGIN_LINK,
        self::PLUGIN_MEDIA_EMBED,
        self::PLUGIN_PASTE_FROM_OFFICE,
        self::PLUGIN_TABLE,
        self::PLUGIN_TABLE_TOOLBAR,
    ];
    protected $tools = [
        self::TOOL_BOLD,
        self::TOOL_ITALIC,
        self::TOOL_UNORDERED_LIST,
        //self::TOOL_ORDERED_LIST,
        self::TOOL_REDO,
        self::TOOL_UNDO,
    ];

    const PLUGIN_ESSENTIALS = 'Essentials';
    const PLUGIN_CK_FINDER_UPLOAD_ADAPTER = 'CKFinderUploadAdapter';
    const PLUGIN_AUTO_FORMAT = 'Autoformat';
    const PLUGIN_BOLD = 'Bold';
    const PLUGIN_ITALIC = 'Italic';
    const PLUGIN_BLOCK_QUOTE = 'BlockQuote';
    const PLUGIN_CK_FINDER = 'CKFinder';
    const PLUGIN_EASY_IMAGE = 'EasyImage';
    const PLUGIN_HEADING = 'Heading';
    const PLUGIN_IMAGE = 'Image';
    const PLUGIN_IMAGE_CAPTION = 'ImageCaption';
    const PLUGIN_IMAGE_STYLE = 'ImageStyle';
    const PLUGIN_IMAGE_TOOLBAR = 'ImageToolbar';
    const PLUGIN_IMAGE_UPLOAD = 'ImageUpload';
    const PLUGIN_LINK = 'Link';
    const PLUGIN_LIST = 'List';
    const PLUGIN_MEDIA_EMBED = 'MediaEmbed';
    const PLUGIN_PARAGRAPH = 'Paragraph';
    const PLUGIN_PASTE_FROM_OFFICE = 'PasteFromOffice';
    const PLUGIN_TABLE = 'Table';
    const PLUGIN_TABLE_TOOLBAR = 'TableToolbar';

    const TOOL_BOLD = 'bold';
    const TOOL_ITALIC = 'italic';
    const TOOL_UNORDERED_LIST = 'bulletedList';
    const TOOL_ORDERED_LIST = 'numberedList';
    const TOOL_UNDO = 'Undo';
    const TOOL_REDO = 'Redo';
    const TOOL_BLOCK_QUOTE = 'blockQuote';
    const TOOL_LINK = 'link';
    const TOOL_HEADING = 'heading';
    const TOOL_IMAGE_UPLOAD = 'imageUpload';
    const TOOL_MEDIA_EMBED = 'mediaEmbed';
    const TOOL_INSERT_TABLE = 'insertTable';
    const TOOL_TABLE_COLUMN = 'tableColumn';
    const TOOL_TABLE_ROW = 'tableRow';

    const TOOL_TABLE = 'table';

    /**
     * @param C4GBrickField[] $fieldList
     * @param $data
     * @param C4GBrickDialogParams $dialogParams
     * @param array $additionalParams
     * @return array|string
     */
    public function getC4GDialogField($fieldList, $data, C4GBrickDialogParams $dialogParams, $additionalParams = [])
    {
        $id = 'c4g_ckeditor_' . self::$instances;
        self::$instances += 1;
        $required = $this->generateRequiredString($data, $dialogParams);
        $value = $this->generateInitialValue($data);

        if (!($this->getSize())) {
            $size = 15;
        } else {
            $size = $this->getSize();
        }

        $isReadOnly = false;
        $tools = $this->tools;
        if (!$this->isEditable()) {
            $isReadOnly = true;
            $tools = [];
        }

        $script = 'c4g.ckeditor.create(document.querySelector( \'#' . $id . '\' ), { 
                removePlugins: [ ' . $this->createJSONStringFromArray($this->removedPlugins) . ' ],
                toolbar: [ ' . $this->createJSONStringFromArray($tools) . ' ],
                language: \'de\'
            }).then( editor => { ckeditor5instances[\'#' . $id . '\'] = editor; editor.isReadOnly = \'' . $isReadOnly . '\';} ).catch( error => {console.error(error);});';

        $condition = $this->createConditionData($fieldList, $data);
        $fieldData = '<textarea onfocus="' . $script . '" ' . $required . ' ' . $condition['conditionPrepare'] . 'id="' . $id . '"' . ' class="formdata js-ckeditor' . ' ui-corner-all" name="' . $this->getFieldName() . '" cols="80" rows="' . $size . '" >' . $value . ' </textarea>';
        $fieldData .= '<script>' . $script . '</script>';

        $result = $this->addC4GField($condition, $dialogParams, $fieldList, $data, $fieldData);

        return $result;
    }

    /**
     * @param $dbValues
     * @param $dlgValues
     * @return array|C4GBrickFieldCompare|null
     */
    public function compareWithDB($dbValues, $dlgValues)
    {
        $fieldname = $this->getFieldName();
        $dbValue = $dbValues->$fieldname;
        $dlgvalue = $dlgValues[$this->getFieldName()];
        $dbValue = trim($dbValue);
        $dlgValue = trim($dlgvalue);
        $result = null;
        if (strcmp($dbValue, $dlgValue) != 0) {
            $result = new C4GBrickFieldCompare($this, $dbValue, $dlgValue);
        }

        return $result;
    }

    protected function createJSONStringFromArray(array $array)
    {
        $json = '';
        foreach ($array as $entry) {
            if ($json !== '') {
                $json .= ', ';
            }
            $json .= "'$entry'";
        }

        return $json;
    }

    private function addPlugin(string $plugin)
    {
        if (in_array($plugin, $this->removedPlugins)) {
            unset($this->removedPlugins[array_search($plugin, $this->removedPlugins)]);
        }
    }

    /**
     * @param string $tool
     * @return C4GCKEditor5Field
     */
    public function addTool(string $tool): C4GCKEditor5Field
    {
        if (in_array($tool, $this->tools)) {
            return $this;
        }

        switch ($tool) {
            case self::TOOL_BOLD:
            case self::TOOL_ITALIC:
            case self::TOOL_UNDO:
            case self::TOOL_REDO:
            case self::TOOL_TABLE_ROW:
            case self::TOOL_TABLE_COLUMN:
                $this->tools[] = $tool;

                break;
            case self::TOOL_UNORDERED_LIST:
            case self::TOOL_ORDERED_LIST:
                $this->tools[] = $tool;
                $this->addPlugin(self::PLUGIN_LIST);

                break;
            case self::TOOL_BLOCK_QUOTE:
                $this->tools[] = $tool;
                $this->addPlugin(self::PLUGIN_BLOCK_QUOTE);

                break;
            case self::TOOL_LINK:
                $this->tools[] = $tool;
                $this->addPlugin(self::PLUGIN_LINK);

                break;
            case self::TOOL_HEADING:
                $this->tools[] = $tool;
                $this->addPlugin(self::PLUGIN_HEADING);

                break;
            case self::TOOL_IMAGE_UPLOAD:
                $this->tools[] = $tool;
                $this->addPlugin(self::PLUGIN_IMAGE_UPLOAD);
                $this->addPlugin(self::PLUGIN_IMAGE_TOOLBAR);
                $this->addPlugin(self::PLUGIN_IMAGE_STYLE);
                $this->addPlugin(self::PLUGIN_IMAGE_CAPTION);

                break;
            case self::TOOL_MEDIA_EMBED:
                $this->tools[] = $tool;
                $this->addPlugin(self::PLUGIN_MEDIA_EMBED);

                break;
            case self::TOOL_INSERT_TABLE:
                $this->tools[] = $tool;
                $this->addPlugin(self::PLUGIN_TABLE);

                break;
            case self::TOOL_TABLE:
                $this->tools[] = self::TOOL_INSERT_TABLE;
                $this->addPlugin(self::PLUGIN_TABLE);
                $this->addPlugin(self::PLUGIN_TABLE_TOOLBAR);

                break;
            default:
                break;
        }

        return $this;
    }

    /**
     * @param string $tool
     * @return C4GCKEditor5Field
     */
    public function removeTool(string $tool): C4GCKEditor5Field
    {
        if (in_array($tool, $this->tools)) {
            unset($this->tools[array_search($tool, $this->tools)]);
        }

        return $this;
    }

    /**
     * @return C4GCKEditor5Field
     */
    public function clearTools(): C4GCKEditor5Field
    {
        $this->tools = [];

        return $this;
    }
}
