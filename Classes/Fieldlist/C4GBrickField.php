<?php
/**
 * con4gis - the gis-kit
 *
 * @version   php 7
 * @package   con4gis
 * @author    con4gis contributors (see "authors.txt")
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2018
 * @link      https://www.kuestenschmiede.de
 */

namespace con4gis\ProjectsBundle\Classes\Fieldlist;

use con4gis\CoreBundle\Resources\contao\classes\C4GHTMLFactory;
use con4gis\CoreBundle\Resources\contao\classes\C4GUtils;
use con4gis\ProjectsBundle\Classes\Conditions\C4GBrickConditionType;
use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialogParams;
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GButtonField;
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GDecimalField;
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GSubDialogField;
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GEmailField;
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GLinkField;
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GNumberField;
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GUrlField;
use con4gis\ProjectsBundle\Classes\Views\C4GBrickViewType;

abstract class C4GBrickField
{
    /**
     * Properties
     *
     * @property string $align Where the field content aligns to. Valid values are 'left', 'center' and 'right'. Default: 'left'.
     * @property string $description Adds a description under the input field. Default: empty.
     * @property string $fieldName The name of the field in the database corresponding to this field. Default: null.
     * @property bool $initInvisible Press true to force the field to be invisible initially. Default: false. (Only works with fields that use the generateC4GFieldHTML method)
     * @property bool $mandatory Whether this field is mandatory. Default: false.
     * @property int $size Size for some field types. Default: 0.
     * @property string $specialMandatoryMessage Set an individual message for this mandatory field. Default: empty.
     * @property string $styleClass Additional css class for this field . Default: empty.
     * @property bool $tableColumn Whether this field is shown in the data table. Default: false.
     *
     */
    //Todo globale Properties prüfen und ggf. den Feldklassen zuordnen

    //please follow alphabetical sorting
    private $action = array(); //action for action callable fieldtypes

    /**
     * @var string
     */
    private $align = 'left';
    private $additionalID = ''; //additional ID for switchable (hidden) Fields with same fieldname
    private $addressField = null; //nominatim reverse search
    private $callOnChange = false; //call function on change (useful with select and other types)
    private $callOnChangeFunction = 'C4GCallOnChange(this)'; //call this function on change
    private $columnWidth = 0; //culumn width on datatable view
    private $comparable = true; //for field Compare on saving
    private $condition = array(); //see C4GBrickCondition
    private $contentId = ''; //for transfer contao content elements like Map
    private $databaseField = true; //is this field with database mapping (saving, compare, ...)
    private $dbUnique = false; //unique field value
    private $dbUniqueResult = ''; //Achtung! Der Result Text muss im Modul vergeben werden, da die Abfrage über die ganze Datenbank alle Mitglieder betrifft und das Ergebnis entsprechend sensibel ist.
    private $dbUniqueAdditionalCondition = ''; //Zusätzliche Bedingungen die in der Query berücksichtigt werden können.
    private $description = ''; //field description (under input field)
    private $display = true; // if false, the field is hidden & will not be checked during the mandatory check
    private $editable = true; //is the field editable?

    //new
    private $addStrBeforeValue = '';
    private $addStrBehindValue = '';

    //special properties for external table fields
    private $externalCallBackFunction = null;
    private $externalFieldName = null;
    private $externalIdField = null;
    private $externalModel = null;
    private $externalSearchValue = null;
    private $externalSortField = null;

    private $extTitleField = null; //Sonderlocke für Feld in Feld zum Beispiel für Feldbewertungen
    private $fieldName = null; //the fieldName should be conform to the database field name
    private $fileTypes = ''; //see C4GBrickFileType
    private $formField = true; //you can use this property if the field shouldn't be part of the formular
    private $hidden = false; //for hidden fields
    private $ignoreViewType = false; //special to set over viewType properties like disabled fields
    private $initialFields = array(); //z.B. für die Bildgallerie
    private $initialValue = ''; //set the initial value for the field (presetting)
    private $insertLink = ''; //???
    private $latitudeField = 'loc_geoy'; //Coordinate for geopicker field
    private $loadOptions = null; //see C4GBrickLoadOptions
    private $longitudeField = 'loc_geox'; //Coordinate for geopicker field
    private $mandatory = false; //mandatory field?
    private $max = 99999; //max value
    private $maxLength = 1024; //max length
    private $min = 0; //min value
    private $notificationField = false; // for Notification Center
    private $options = array(); //Options for select boxes, radio groups, ...
    private $popupField = true; // show in popup or not
    private $radiusFieldName = ''; //???
    private $randomValue  = ''; //Use this instead of initialValue if the value is randomly generated. The generated value must not be an empty string. If the Field is not a FormField, use initialValue.
    private $searchField = false; //für C4GMatching
    private $searchMaximumField = false; // C4GMatching
    private $searchMinimumField = false; // C4GMatching
    private $searchWeightings = 1; //für C4GMatching
    private $showIfEmpty = true; //do not show clear fields?
    private $size = 0; //size for some types
    private $sort = true; //activate sorting for fieldtypes with options
    private $sortColumn = false; //is this a sort column in datatable?
    private $sortSequence = 'asc'; //sort sequence
    private $sortType = ''; //sort type de_date, de_datetime
    private $source = C4GBrickFieldSourceType::DATABASE; //datasource, if not database
    private $sourceField = null; //field content not database field
    private $styleClass = ''; //additional css class for this field
    private $tableColumn = false; //show this field in datatable?
    private $tableColumnPriority = 0; //lowest values (> 0) will be removed last on small displays.
    private $tableRow = false; //show field as table row -> label, input in one row
    private $tableRowLabelWidth = '25%'; //table row label width
    private $tableRowWidth = '98.2%'; //table row width
    private $testValue = ''; //so use can optional a check value if needed
    private $title = ''; //field title (label)
    private $type = '';  //siehe C4GBrickFieldType
    private $withoutDescriptionLineBreak = false; //no additonal line break behind description
    private $withEmptyOption = false; //default empty option for fieldtypes with options like select box
    private $withoutLabel = false; //do not show the label
    private $unique = false; //??? unique vs. dbunique
    private $specialMandatoryMessage = ''; //do not show the default mandatory message
    private $switchTitleLabel = false; //change order label, input
    private $withoutMandatoryStar = false; //do not show the mandatory star
    private $conditionalDisplay = false; // set true if field should be hidden dependent on the value of another field
    private $conditionalFieldName = null; // the fieldname of the field which is the condition
    private $displayValue = null; // the value of the condition field at which this field will be displayed
    private $removeWithEmptyCondition = false; // soll das Feld ohne Conditions ausgeblendet werden (valueSwitch)
    private $showSum = false; // zeigt eine zusätzliche Footer Zeile mit der Spaltensumme.
    private $tileClass = false; //soll die value als tile class gesetzt werden.
    private $tileClassTable = ''; // soll der value für die class aus einer Tabelle geholt werden?
    private $tileClassField = ''; // aus welchem Feld soll der value für die class geholt werden?
    protected $initInvisible = false;  //ToDo das gehört in die Felder rein, die es auch benutzen können
    private $withLinkDescription = false;
    private $conditionType = null; //see C4GBrickConditionType
    private $additionalLabel = ''; //Additional String to be added to the label, e.g.

    /**
     * C4GBrickField constructor.
     */
    public function __construct()
    {

    }

    /**
     * Public method for creating the field specific dialog HTML
     * @param C4GBrickField[] $fieldList
     * @param $data
     * @param $dialogParams
     * @param array $additionalParams
     * @return array
     */
    public abstract function getC4GDialogField($fieldList, $data, C4GBrickDialogParams $dialogParams, $additionalParams = array());

    /**
     * Public method for generating the HTML code for fields based on the parameters given by the child class.
     * Used only by the Headline and RadioGroup fields.
     * @param string $class The css class to be used.
     * @param array $condition The condition parameters to be displayed in the html tag.
     * @param string $html The field-specific HTML code
     * @return string The generated HTML code
     */

    public function generateC4GFieldHTML($condition, $html, $class = '')
    {
        $display = '';

        if ($class == '') {
            $class = 'class="' . $this->styleClass . '"';
        } else {
            $class = 'class="' . $class . '"';
        }

        if ($this->initInvisible) {
            $display = 'style="display: none"';
        }
        return '<div id="c4g_condition" '
        . $class
        . $condition['conditionName']
        . $condition['conditionType']
        . $condition['conditionValue']
        . $condition['conditionFunction']
        . $condition['conditionDisable']
        . $display . '>'
        . $html
        .'</div>';
    }

    /**
     * Public method for creating the field specific list HTML
     * @param $rowData
     * @param $content
     * @return mixed
     */
    public function getC4GListField($rowData, $content)
    {
        $fieldName = $this->getFieldName();

        $value = $rowData->$fieldName;
        if ($this->getAddStrBeforeValue()) {
            $value = $this->getAddStrBeforeValue().$value;
        }
        if ($this->getAddStrBehindValue()) {
            $value = $value.$this->getAddStrBehindValue();
        }

        return $value;
    }

    /**
     * Public method for creating the field specific tile HTML
     * @param $fieldTitle
     * @param $element
     * @return mixed
     */
    public function getC4GTileField($fieldTitle, $element)
    {
        $fieldName = $this->getFieldName();
        return $element->$fieldName;
    }

    public function getC4GPopupField($data, $groupId)
    {
        if ($data[$this->getFieldName()]) {
            $styleClass = $this->getStyleClass();
            if ($this->isWithoutLabel()) {
                return "<div class=".$styleClass.">" . $data[$this->getFieldName()] ."</div>";
            } else {
                return "<p class=".$styleClass."><b>". $this->getTitle() . "</b>: " . $data[$this->getFieldName()] ."</p>";
            }
        } else {
            return '';
        }
    }

    /**
     * Public method that will be called in translateFieldValues in C4GBrickModuleParent
     * @param $value
     * @return mixed
     */
    public function translateFieldValue($value)
    {
        return $value;
    }

    public function validateFieldValue($value)
    {
        return $value;
    }

    /**
     * Method that will be called in the compareWithDB() in C4GBrickDialog
     * @param $dbValue
     * @param $dlgvalue
     * @return array
     */
    public abstract function compareWithDB($dbValue, $dlgvalue);

    /**
     * Method that will be called in the saveC4GDialog() in C4GBrickDialog
     * @return array
     */
    public function createFieldData($dlgValues) {
        if(($this instanceof C4GDecimalField || $this instanceof C4GNumberField) && $this->getThousandsSep() !== ''){
            $value = str_replace($this->getThousandsSep(),'',$dlgValues[$this->getFieldName()]);
            $dlgValues[$this->getFieldName()] = $value;
        }
        return $dlgValues[$this->getFieldName()];
    }

    /**
     * @param $fieldList
     * @param $data
     * @param $condition
     * @return bool
     */
    /** Beginn Formularfelder */
    /**
     * @return string
     */
    protected function addC4GLabel($label)
    {
        return '<label class="c4g_label">' . $label . '</label>';
    }

    /** Beginn Formularfelder */
    /**
     * @return string
     */
    protected function addC4GFieldLabel($id, $title, $mandatory, $condition, $fieldList, $data, $dialogParams, $showExtTitleField = true, $withoutLineBreak = false)
    {
        if ($title && !$this->withoutLabel || $this instanceof C4GButtonField || $this instanceof C4GLinkField || $this instanceof C4GSubDialogField) {
            $star = '';
            if ($mandatory && (!$this->isWithoutMandatoryStar())) {
                $star = '<strong class="c4g_mandatory_class">*</strong>';
            }
            if(!$withoutLineBreak){
                $linebreak = C4GHTMLFactory::lineBreak();
            }
            $tdo = '';
            $tdc = '';
            if (($dialogParams && $dialogParams->isTableRows()) || $this->isTableRow()) {
                $linebreak = '';
                $tdo = '<td style="width:'.$this->getTableRowLabelWidth().'">';
                $tdc = '</td>';
            }

            if ($showExtTitleField && $this->getExtTitleField()) {
                $extTitleField = $this->getExtTitleField()->getC4GDialogField($fieldList, $data, $dialogParams);
            }
            $additionalLabel = '';
            if ($this->getAdditionalLabel()) {
                $additionalLabel = $this->getAdditionalLabel();
            }
            if($this->isWithoutLabel()){
                return $tdo.'<label for="' . $id . '" ' . $condition['conditionPrepare'] . '>' . $star . $additionalLabel . $linebreak . '</label>'.$tdc.$extTitleField;
            } else {
                return $tdo.'<label for="' . $id . '" ' . $condition['conditionPrepare'] . '>' . $star . $title . $additionalLabel . $linebreak . '</label>'.$tdc.$extTitleField;
            }
        } else {
            return '';
        }
    }

    public function checkCondition($fieldList, $data, $conditions)
    {
        if ($conditions) {
            $emptyConditionFieldData = false;

            //alle Feldbedingungen werden durchlaufen
            foreach ($conditions as $condition) {
                if (empty($condition)) {
                    continue;
                }

                $conditionField = $condition->getFieldName(); //Feldname von dem schaltenden Feld
                $conditionValue = $condition->getValue(); //Feldvalue von dem schaltenden Feld

                switch ($condition->getType()) {
                    case C4GBrickConditionType::BOOLSWITCH:
                        //bisher deaktivert der Boolswitch lediglich Felder, deshalb muss hier true zurückgegeben werden,
                        //ansonsten würden die Felder ausgeblendet werden.
                        return true;//($data->$conditionField == $conditionValue);
                    case C4GBrickConditionType::VALUESWITCH:

                        foreach ($fieldList as $listField) {
                            //Ist das das schaltende Feld?
                            if ($listField->getAdditionalID()) {
                                if ($conditionField == $listField->getFieldName() . '_' . $listField->getAdditionalID()) {
                                    if (($data) && ($data->$conditionField)) {
                                        //der aktuelle Wert aus der Datenbank
                                        $conditionFieldData = $data->$conditionField;
                                    } else {
                                        //der initial Wert, falls (noch) kein Datenbankwert vorhanden ist
                                        $conditionFieldData = $listField->getInitialValue();
                                    }

                                    // ist der aktuelle Wert der freischaltende Wert?
                                    if ($conditionFieldData == $conditionValue) {
                                        return true;
                                    } else {
                                        if (empty($conditionFieldData) || ($conditionFieldData == -1)) {
                                            $emptyConditionFieldData = true;
                                        }
                                    }
                                }
                            } else {
                                //Ist das das schaltende Feld?
                                if ($conditionField == $listField->getFieldName()) {
                                    if (($data) && ($data->$conditionField)) {
                                        //der aktuelle Wert aus der Datenbank
                                        $conditionFieldData = $data->$conditionField;
                                    } else {
                                        //der initial Wert, falls (noch) kein Datenbankwert vorhanden ist
                                        $conditionFieldData = $listField->getInitialValue();
                                    }

                                    // ist der aktuelle Wert der freischaltende Wert?
                                    if ($conditionFieldData == $conditionValue) {
                                        return true;
                                    } else {
                                        if (empty($conditionFieldData) || ($conditionFieldData == -1)) {
                                            $emptyConditionFieldData = true;
                                        }
                                    }
                                }
                            }
                        }
                        break;
                    case C4GBrickConditionType::METHODSWITCH:
                        $conditionField = $condition->getFieldName();
                        //$conditionValue = $condition->getValue();
                        $conditionModel = $condition->getModel();
                        $conditionFunction = $condition->getFunction();

                        foreach ($fieldList as $listField) {
                            if ($listField->getAdditionalID()) {
                                if ($conditionField == $listField->getFieldName() . '_' . $listField->getAdditionalID()) {
                                    if (($data) && ($data->$conditionField)) {
                                        $conditionFieldData = $data->$conditionField;
                                    } else {
                                        $conditionFieldData = $listField->getInitialValue();
                                    }

                                    if ($conditionModel && $conditionFunction) {
                                        return $conditionModel::$conditionFunction($conditionFieldData);
                                    } else {
                                        return false;
                                    }
                                }
                            } else if ($conditionField == $listField->getFieldName()) {
                                if (($data) && ($data->$conditionField)) {
                                    $conditionFieldData = $data->$conditionField;
                                } else {
                                    $conditionFieldData = $listField->getInitialValue();
                                }

                                if ($conditionModel && $conditionFunction) {
                                    return $conditionModel::$conditionFunction($conditionFieldData);
                                } else {
                                    return false;
                                }
                            }
                        }
                        break;
                }
            }

        }

        return false;/*!$emptyConditionFieldData*/
    }
    /**
     * @param $description
     * @return string
     */
    protected function getC4GDescriptionLabel($description, $condition)
    {
        $withLinkDescription = $this->isWithLinkDescription();
        $withoutLineBreak = $this->isWithoutDescriptionLineBreak();

        $result = '';
        if ($description && ($description != '') && $withLinkDescription) {
            $result = '<p class="c4g_field_description" ' . $condition['conditionPrepare'] . '><a href="' . $description . '" target="_blank">Link</a>' . C4GHTMLFactory::lineBreak() . C4GHTMLFactory::lineBreak() . '</p>';
        } else if ($description && ($description != '')) {
            $result = '<p class="c4g_field_description" ' . $condition['conditionPrepare'] . '>' . $description . C4GHTMLFactory::lineBreak() . C4GHTMLFactory::lineBreak() . '</p>';
        } else if (!$withoutLineBreak) {
            $result = '<p ' . $condition['conditionPrepare'] . '>' . '</p>';
        } else {
            $result = '<div class="c4g_field_descripton_hole"></div>';
        }

        return $result;
    }



    protected function createConditionData($fieldList, $data)
    {
        $conditions = $this->getCondition();
        $conditionname = '';
        $conditiontype = '';
        $conditionvalue = '';
        $conditiondisable = '';
        $conditionPrepare = '';
        $conditionfunction = '';

        $class = 'formdata';

        if (!empty($conditions)) {
            $conditionResult = $this->checkCondition($fieldList, $data, $conditions);

            if (!$conditionResult) {
                $conditionPrepare = 'style="display: none;"';
            }

            foreach ($conditions as $condition) {
                if (empty($condition)) {
                    continue;
                }

                if (!empty($conditionname) OR $conditionname == '0') {
                    $conditionname .= '~' . $condition->getFieldName();
                    $conditionvalue .= '~' . $condition->getValue();
                    $conditionfunction .= '~' . $condition->getFunction();
                } else {
                    $conditionname = $condition->getFieldName();
                    $conditionvalue = $condition->getValue();
                    $conditionfunction .= $condition->getFunction();
                }

                $conditiontype = $condition->getType();

            }

            if (!$this->isEditable()) {
                $conditiondisable = 'true';
            } else {
                $conditiondisable = 'false';
            }
        } else if ($this->isRemoveWithEmptyCondition()) {
            $conditionPrepare = 'style="display: none;"';
        }

        return array(
            'conditionResult' => $conditionResult,
            'conditionPrepare' => $conditionPrepare,
            'class' => $class,
            'conditionName' => 'data-condition-name="' . $conditionname . '"',
            'conditionType' => 'data-condition-type="' . $conditiontype . '"',
            'conditionValue' => 'data-condition-value="' . $conditionvalue . '"',
            'conditionFunction' => 'data-condition-function="' . $conditionfunction . '"',
            'conditionDisable' => 'data-condition-disable="' . $conditiondisable . '"',
        );
    }

    public function addC4GField($condition, $dialogParams, $fieldList, $data, $fieldData) {
        //ToDo change table to display:grid if feature released for all standard browsers
        $id = "c4g_" . $this->getFieldName();
        $value = $this->generateInitialValue($data);

        if($value && ($this instanceof C4GEmailField || $this instanceof C4GUrlField) && $this->isWithLinkDescription()) {
            if($this instanceof C4GEmailField) {
                $description = $this->getC4GDescriptionLabel('mailto:'.$value, $condition);
            } else {
                if($this->isExternalLink()){
                    if(!(C4GUtils::startsWith($value,'http') || C4GUtils::startsWith($value, 'https'))) {
                        $value = 'http://'.$value;
                    }
                }
                $description = $this->getC4GDescriptionLabel($value, $condition);
            }

        } else {
            if ($dialogParams->isWithDescriptions()) {
                $description = $this->getC4GDescriptionLabel($this->getDescription(), $condition);
            }
        }
        $tableo = '';
        $tablec = '';
        $tdo = '';
        $tdc = '';
        $tro = '';
        $trc = '';

        if (($dialogParams && $dialogParams->isTableRows()) || $this->isTableRow()) {
            //$linebreak = '';
            $tableo = '<table class="c4g_brick_table_rows" style="width:'.$this->getTableRowWidth().'">';
            $tro = '<tr>';
            $trc = '</tr>';
            $tdo = '<td>';
            $tdc = '</td>';
            $tablec = '</table>';
        }

        $class = '';
        if ($this->getStyleClass()) {
            $class = 'class="'.$this->getStyleClass() .'" ';
        }

        $fieldLabel = $this->addC4GFieldLabel($id, $this->getTitle(), $this->isMandatory(), $condition, $fieldList, $data, $dialogParams, true, $this->switchTitleLabel);

        if ($this->switchTitleLabel) {
            $label = $fieldLabel;
            $data = $fieldData;
            $fieldLabel = $data;
            $fieldData  = $label;
        }

        if ($this->isConditionalDisplay()) {
            // isset seems not to work with expression results
            if ($this->getConditionalFieldName() != '' && ($this->displayValue !== '-1')) {
                // all required properties are set
                $string = ' data-condition-field="c4g_'. $this->getConditionalFieldName() .'" data-condition-value="'. $this->getDisplayValue() .'" ';
                $class .= $string;

            }
        }

        return '<div id="c4g_condition" '
        . $class
        . $condition['conditionName']
        . $condition['conditionType']
        . $condition['conditionValue']
        . $condition['conditionFunction']
        . $condition['conditionDisable']  . '>' .
        $tableo.$tro.$fieldLabel.
        $tdo.$fieldData.$tdc.$trc.$tablec.
        $description . '</div>';
    }

    /**
     * @param $this
     * @param $data
     * @return mixed
     */
    protected function generateInitialValue($data)
    {
        if (((!$data)) ||
                (!$this->isDatabaseField()) && ($this->getSource() != C4GBrickFieldSourceType::OTHER_FIELD) &&
                (!$this->getExternalIdField()))
        {
            $value = $this->getInitialValue();
        } else {
            $fieldName = $this->getFieldName();
            if ($this->getAdditionalID()) {

                $pos = strripos($fieldName, '_');
                if ($pos !== false) {
                    $fieldName = substr($fieldName, 0, $pos);
                }
            }

            $value = $data->$fieldName;
        }

        if ($this->getAddStrBeforeValue()) {
            $value = $this->getAddStrBeforeValue().$value;
        }
        if ($this->getAddStrBehindValue()) {
            $value = $value.$this->getAddStrBehindValue();
        }

        return $value;
    }

    /**
     * @param $data
     * @param C4GBrickDialogParams $dialogParams
     * @return string
     */
    protected function generateRequiredString($data, $dialogParams)
    {
        $required = "";
        if ($this->getConditionType() == C4GBrickConditionType::BOOLSWITCH) {
            $condition = $this->getCondition();
            if ($condition) {
                $thisName = $condition[0]->getFieldName();
                if ($data && ($data->$thisName != $condition[0]->getValue())) {
                    $required = "disabled readonly";
                    $this->setWithoutMandatoryStar(true);
                    $this->setEditable(false);
                    return $required;
                }

            }
        }

        $viewType = $dialogParams->getViewType();

        if (!$this->ignoreViewType && $viewType && (
                ($viewType == C4GBrickViewType::PUBLICVIEW) ||
                ($viewType == C4GBrickViewType::PUBLICPARENTVIEW) ||
                ($viewType == C4GBrickViewType::GROUPVIEW) ||
                ($viewType == C4GBrickViewType::PROJECTPARENTVIEW) ||
                ($viewType == C4GBrickViewType::MEMBERVIEW) ||
                ($viewType == C4GBrickViewType::PUBLICUUIDVIEW) ||
                ($dialogParams->isFrozen() == 1))
        ) {
            $required = "disabled readonly";
            $this->setWithoutMandatoryStar(true);
            $this->setEditable(false);
            return $required;

        }

        if ($this->isMandatory()) {
            $required = "required";
        }

        if (!$this->isEditable()) {
            if ($required != "") {
                $required .= " disabled readonly";
            } else {
                $required = "disabled readonly";
            }
        }

        return $required;
    }

    protected function createFieldID()
    {
        return "c4g_" . $this->getFieldName();
    }

    /**
     * Returns false if the field is not mandatory or if it is mandatory but its conditions are not met.
     * Otherwise it checks whether the field has a valid value and returns the result.
     * @param array $dlgValues
     * @return bool|C4GBrickField
     */

    public function checkMandatory($dlgValues)
    {
        //$this->specialMandatoryMessage = $this->fieldName;    //Useful for debugging
        if (!$this->mandatory) {
            return false;
        } elseif(!$this->display) {
            return false;
        } elseif ($this->condition) {
            foreach ($this->condition as $con) {
                if (empty($con)) {
                    continue;
                }
                $fieldName = $con->getFieldName();
                if (!$con->checkAgainstCondition($dlgValues[$fieldName])) {
                    return false;
                }
            }
        }
        $fieldName = $this->fieldName;
        $fieldData = $dlgValues[$fieldName];
        if (is_string($dlgValues[$fieldName])) {
            $fieldData = trim($fieldData);
        }
        if (($fieldData == null) || ($fieldData) == '') {
            return $this;
        } else {
            return false;
        }
    }

    /**
     * @return string
     */
    public function getRadiusFieldName()
    {
        return $this->radiusFieldName;
    }

    /**
     * @param $radiusFieldName
     * @return $this
     */
    public function setRadiusFieldName($radiusFieldName)
    {
        $this->radiusFieldName = $radiusFieldName;
        return $this;
    }

    /**
     * @return null
     */
    public function getAddressField()
    {
        return $this->addressField;
    }

    /**
     * @param $addressField
     * @return $this
     */
    public function setAddressField($addressField)
    {
        $this->addressField = $addressField;
        return $this;
    }

    /**
     * @return null
     */
    public function getExternalSortField()
    {
        return $this->externalSortField;
    }

    /**
     * @param $externalSortField
     * @return $this
     */
    public function setExternalSortField($externalSortField)
    {
        $this->externalSortField = $externalSortField;
        return $this;
    }


    /**
     * @return null
     */
    public function getExternalSearchValue()
    {
        return $this->externalSearchValue;
    }

    /**
     * @param $externalSearchValue
     * @return $this
     */
    public function setExternalSearchValue($externalSearchValue)
    {
        $this->externalSearchValue = $externalSearchValue;
        return $this;
    }


    /**
     * @return string
     */
    public function getExternalModel()
    {
        return $this->externalModel;
    }

    /**
     * @param $externalModel
     * @return $this
     */
    public function setExternalModel($externalModel)
    {
        $this->externalModel = $externalModel;
        return $this;
    }

    /**
     * @return string
     */
    public function getExternalIdField()
    {
        return $this->externalIdField;
    }

    /**
     * @param $externalIdField
     * @return $this
     */
    public function setExternalIdField($externalIdField)
    {
        $this->externalIdField = $externalIdField;
        return $this;
    }

    /**
     * @return null
     */
    public function getExternalFieldName()
    {
        return $this->externalFieldName;
    }

    /**
     * @param $externalFieldName
     * @return $this
     */
    public function setExternalFieldName($externalFieldName)
    {
        $this->externalFieldName = $externalFieldName;
        return $this;
    }

    /**
     * @return string
     */
    public function getMaxLength()
    {
        return $this->maxLength;
    }

    /**
     * @param $maxLength
     * @return $this
     */
    public function setMaxLength($maxLength)
    {
        $this->maxLength = $maxLength;
        return $this;
    }

    /**
     * @return string
     */
    public function getLatitudeField()
    {
        return $this->latitudeField;
    }

    /**
     * @param $latitudeField
     * @return $this
     */
    public function setLatitudeField($latitudeField)
    {
        $this->latitudeField = $latitudeField;
        return $this;
    }

    /**
     * @return string
     */
    public function getLongitudeField()
    {
        return $this->longitudeField;
    }

    /**
     * @param $longitudeField
     * @return $this
     */
    public function setLongitudeField($longitudeField)
    {
        $this->longitudeField = $longitudeField;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isNotificationField()
    {
        return $this->notificationField;
    }

    /**
     * @param bool $notificationField
     * @return $this
     */
    public function setNotificationField($notificationField = true)
    {
        $this->notificationField = $notificationField;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isSearchField()
    {
        return $this->searchField;
    }

    /**
     * @param bool $searchField
     * @return $this
     */
    public function setSearchField($searchField = true)
    {
        $this->searchField = $searchField;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isSearchMinimumField()
    {
        return $this->searchMinimumField;
    }

    /**
     * @param bool $searchMinimumField
     * @return $this
     */
    public function setSearchMinimumField($searchMinimumField = true)
    {
        $this->searchMinimumField = $searchMinimumField;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isSearchMaximumField()
    {
        return $this->searchMaximumField;
    }

    /**
     * @param bool $searchMaximumField
     * @return $this
     */
    public function setSearchMaximumField($searchMaximumField = true)
    {
        $this->searchMaximumField = $searchMaximumField;
        return $this;
    }


    /**
     * @return int
     */
    public function getSearchWeightings()
    {
        return $this->searchWeightings;
    }

    /**
     * @param $searchWeightings
     * @return $this
     */
    public function setSearchWeightings($searchWeightings)
    {
        $this->searchWeightings = $searchWeightings;
        return $this;
    }



    /**
     * @return string
     */
    public function getSourceField()
    {
        return $this->sourceField;
    }

    /**
     * @param $sourceField
     * @return $this
     */
    public function setSourceField($sourceField)
    {
        $this->sourceField = $sourceField;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isWithLinkDescription()
    {
        return $this->withLinkDescription;
    }

    /**
     * @param bool $withLinkDescription
     * @return $this
     */
    public function setWithLinkDescription($withLinkDescription = true)
    {
        $this->withLinkDescription = $withLinkDescription;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isWithEmptyOption()
    {
        return $this->withEmptyOption;
    }

    /**
     * @param bool $withEmptyOption
     * @return $this
     */
    public function setWithEmptyOption($withEmptyOption = true)
    {
        $this->withEmptyOption = $withEmptyOption;
        return $this;
    }

    /**
     * @param $additionalID
     * @return $this
     */
    public function setAdditionalID($additionalID)
    {
        $this->additionalID = $additionalID;
        return $this;
    }

    public function getAdditionalID()
    {
        return $this->additionalID;
    }


    /**
     * @return string
     */
    public function getInsertLink()
    {
        return $this->insertLink;
    }

    /**
     * @param string $insertLink  Like {{link_url::??}}
     * @return $this
     */
    public function setInsertLink($insertLink)
    {
        $this->insertLink = $insertLink;
        return $this;
    }


    /**
     * @return array
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param $actionType z.B. für die Bildgallerie
     * @param $actionEvent
     * @return $this
     */
    public function setAction($actionType, $actionEvent)
    {
        $this->action = array('actiontype' => $actionType, 'actionevent' => $actionEvent);
        return $this;
    }



    /**
     * @return boolean
     */
    public function isComparable()
    {
        return $this->comparable;
    }

    /**
     * @param bool $comparable
     * @return $this
     */
    public function setComparable($comparable = true)
    {
        $this->comparable = $comparable;
        return $this;
    }


    /**
     * @return boolean
     */
    public function isShowIfEmpty()
    {
        return $this->showIfEmpty;
    }

    /**
     * @param bool $showIfEmpty
     * @return $this
     */
    public function setShowIfEmpty($showIfEmpty = true)
    {
        $this->showIfEmpty = $showIfEmpty;
        return $this;
    }

    /**
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param $source
     * @return $this
     */
    public function setSource($source)
    {
        $this->source = $source;
        return $this;
    }



    /**
     * @return null
     */
    public function getLoadOptions()
    {
        return $this->loadOptions;
    }

    /**
     * @param $loadOptions
     * @return $this
     */
    public function setLoadOptions($loadOptions)
    {
        $this->loadOptions = $loadOptions;
        return $this;
    }

    /**
     * @return array
     */
    public function getInitialFields()
    {
        return $this->initialFields;
    }

    /**
     * @param $initialFields
     * @return $this
     */
    public function setInitialFields($initialFields)
    {
        $this->initialFields = $initialFields;
        return $this;
    }

    /**
     * @param $conditionType
     * @return $this
     */
    public function setConditionType($conditionType)
    {
        $this->conditionType = $conditionType;
        return $this;
    }
    
    public function getConditionType()
    {
        return $this->conditionType;
    }

    /**
     * @return null
     */
    public function getCondition()
    {
        return $this->condition;
    }

    /**
     * @param $condition
     * @return $this
     */
    public function setCondition($condition)
    {
        if (is_array($condition)) {
            $this->condition = $condition;
        } else {
            $this->condition[] = $condition;
        }
        return $this;
    }


    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param $description
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    } //siehe C4GBrickFileType


    /**
     * @return string
     */
    public function getFileTypes()
    {
        return $this->fileTypes;
    }

    /**
     * @param $fileTypes
     * @return $this
     */
    public function setFileTypes($fileTypes)
    {
        $this->fileTypes = $fileTypes;
        return $this;
    }

    /**
     * @return string
     */
    public function getContentId()
    {
        return $this->contentId;
    }

    /**
     * @param $contentId
     * @return $this
     */
    public function setContentId($contentId)
    {
        $this->contentId = $contentId;
        return $this;
    } //zur Einbindung von Inhaltselementen in die Maske


    /**
     * @return boolean
     */
    public function isDatabaseField()
    {
        return $this->databaseField;
    }

    /**
     * @param bool $databaseField
     * @return $this
     */
    public function setDatabaseField($databaseField = true)
    {
        $this->databaseField = $databaseField;
        return $this;
    }

    /**
     * @return null
     */
    public function getInitialValue()
    {
        return $this->initialValue;
    }

    /**
     * @param $initialValue
     * @return $this
     */
    public function setInitialValue($initialValue)
    {
        $this->initialValue = $initialValue;
        return $this;
    }

    /**
     * @return string
     */
    public function getSortSequence()
    {
        return $this->sortSequence;
    }

    /**
     * @param $sortSequence
     * @return $this
     */
    public function setSortSequence($sortSequence)
    {
        $this->sortSequence = $sortSequence;
        return $this;
    }

    /**
     * @return string
     */
    public function getSortType()
    {
        return $this->sortType;
    }

    /**
     * @param $sortType
     * @return $this
     */
    public function setSortType($sortType)
    {
        $this->sortType = $sortType;
        return $this;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param $options
     * @return $this
     */
    public function setOptions($options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * @param $model
     * @param string $captionField
     * @return $this
     */
    public function setOptionsByModel($model, $captionField = 'caption')
    {
        $result = array();
        if ($model) {
            $options = $model::findAll();
            if ($options) {
                foreach($options as $option) {
                    $idList[] = array(
                        'id'     => $option->id,
                        'name'   => $option->$captionField);
                }
                $result = $idList;
            }
        }

        $this->setOptions($result);
        return $this;
    }



    /**
     * @return boolean
     */
    public function isFormField()
    {
        return $this->formField;
    }

    /**
     * @param bool $formField
     * @return $this
     */
    public function setFormField($formField = true)
    {
        $this->formField = $formField;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isEditable()
    {
        return $this->editable;
    }

    /**
     * @param bool $editable
     * @return $this
     */
    public function setEditable($editable = true)
    {
        $this->editable = $editable;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isMandatory()
    {
        return $this->mandatory;
    }

    /**
     * @param bool $mandatory
     * @return $this
     */
    public function setMandatory($mandatory = true)
    {
        $this->mandatory = $mandatory;
        return $this;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param $size
     * @return $this
     */
    public function setSize($size)
    {
        $this->size = $size;
        return $this;
    }

    /**
     * @return int
     */
    public function getMin()
    {
        return $this->min;
    }

    /**
     * @param $min
     * @return $this
     */
    public function setMin($min)
    {
        $this->min = $min;
        return $this;
    }

    /**
     * @return int
     */
    public function getMax()
    {
        return $this->max;
    }

    /**
     * @param $max
     * @return $this
     */
    public function setMax($max)
    {
        $this->max = $max;
        return $this;
    }

    /**
     * @return null
     */
    public function getFieldName()
    {
        return $this->fieldName;
    }

    /**
     * @param $thisName
     * @return $this
     */
    public function setFieldName($thisName)
    {
        $this->fieldName = $thisName;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return null
     */
    public function getExternalCallBackFunction()
    {
        return $this->externalCallBackFunction;
    }

    /**
     * @param $externalCallBackFunction
     * @return $this
     */
    public function setExternalCallBackFunction($externalCallBackFunction)
    {
        $this->externalCallBackFunction = $externalCallBackFunction;
        return $this;
    }

    /**
     * @return int
     */
    public function getColumnWidth()
    {
        return $this->columnWidth;
    }

    /**
     * @param $columnWidth
     * @return $this
     */
    public function setColumnWidth($columnWidth)
    {
        $this->columnWidth = $columnWidth;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isSortColumn()
    {
        return $this->sortColumn;
    }

    /**
     * @param bool $sortColumn
     * @return $this
     */
    public function setSortColumn($sortColumn = true)
    {
        $this->sortColumn = $sortColumn;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isTableColumn()
    {
        return $this->tableColumn;
    }

    /**
     * @param bool $tableColumn
     * @return $this
     */
    public function setTableColumn($tableColumn = true)
    {
        $this->tableColumn = $tableColumn;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isCallOnChange()
    {
        return $this->callOnChange;
    }

    /**
     * @param bool $callOnChange
     * @return $this
     */
    public function setCallOnChange($callOnChange = true)
    {
        $this->callOnChange = $callOnChange;
        return $this;
    }

    /**
     * @return string
     */
    public function getCallOnChangeFunction()
    {
        return $this->callOnChangeFunction;
    }

    /**
     * @param $callOnChangeFunction
     * @return $this
     */
    public function setCallOnChangeFunction($callOnChangeFunction)
    {
        $this->callOnChangeFunction = $callOnChangeFunction;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isUnique()
    {
        return $this->unique;
    }

    /**
     * @param bool $unique
     * @return $this
     */
    public function setUnique($unique = true)
    {
        $this->unique = $unique;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isDbUnique()
    {
        return $this->dbUnique;
    }

    /**
     * @param bool $dbUnique
     * @return $this
     */
    public function setDbUnique($dbUnique = true)
    {
        $this->dbUnique = $dbUnique;
        return $this;
    }

    /**
     * @return string
     */
    public function getDbUniqueResult()
    {
        return $this->dbUniqueResult;
    }

    /**
     * @param $dbUniqueResult
     * @return $this
     */
    public function setDbUniqueResult($dbUniqueResult)
    {
        $this->dbUniqueResult = $dbUniqueResult;
        return $this;
    }

    /**
     * @return string
     */
    public function getDbUniqueAdditionalCondition()
    {
        return $this->dbUniqueAdditionalCondition;
    }

    /**
     * @param $dbUniqueAdditionalCondition
     * @return $this
     */
    public function setDbUniqueAdditionalCondition($dbUniqueAdditionalCondition)
    {
        $this->dbUniqueAdditionalCondition = $dbUniqueAdditionalCondition;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isTableRow()
    {
        return $this->tableRow;
    }

    /**
     * @param bool $tableRow
     * @return $this
     */
    public function setTableRow($tableRow = true)
    {
        $this->tableRow = $tableRow;
        return $this;
    }

    /**
     * @return string
     */
    public function getTableRowWidth()
    {
        return $this->tableRowWidth;
    }

    /**
     * @param $tableRowWidth
     * @return $this
     */
    public function setTableRowWidth($tableRowWidth)
    {
        $this->tableRowWidth = $tableRowWidth;
        return $this;
    }

    /**
     * @return string
     */
    public function getTableRowLabelWidth()
    {
        return $this->tableRowLabelWidth;
    }

    /**
     * @param $tableRowLabelWidth
     * @return $this
     */
    public function setTableRowLabelWidth($tableRowLabelWidth)
    {
        $this->tableRowLabelWidth = $tableRowLabelWidth;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isSort()
    {
        return $this->sort;
    }

    /**
     * @param bool $sort
     * @return $this
     */
    public function setSort($sort = true)
    {
        $this->sort = $sort;
        return $this;
    }

    /**
     * @return null
     */
    public function getExtTitleField()
    {
        return $this->extTitleField;
    }

    /**
     * @param $extTitleField
     * @return $this
     */
    public function setExtTitleField($extTitleField)
    {
        $this->extTitleField = $extTitleField;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isHidden()
    {
        return $this->hidden;
    }

    /**
     * @param bool $hidden
     * @return $this
     */
    public function setHidden($hidden = true)
    {
        $this->hidden = $hidden;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isWithoutLabel()
    {
        return $this->withoutLabel;
    }

    /**
     * @param bool $withoutLabel
     * @return $this
     */
    public function setWithoutLabel($withoutLabel = true)
    {
        $this->withoutLabel = $withoutLabel;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isWithoutDescriptionLineBreak()
    {
        return $this->withoutDescriptionLineBreak;
    }

    /**
     * @param bool $withoutDescriptionLineBreak
     * @return $this
     */
    public function setWithoutDescriptionLineBreak($withoutDescriptionLineBreak = true)
    {
        $this->withoutDescriptionLineBreak = $withoutDescriptionLineBreak;
        return $this;
    }

    /**
     * @return string
     */
    public function getStyleClass()
    {
        return $this->styleClass;
    }

    /**
     * @param $styleClass
     * @return $this
     */
    public function setStyleClass($styleClass)
    {
        $this->styleClass = $styleClass;
        return $this;
    }

    /**
     * @param $styleClass
     * @return $this
     */
    public function addStyleClass($styleClass)
    {
        if ($this->styleClass != '') {
            $this->styleClass .= ' ' . $styleClass;
        } else {
            $this->styleClass = $styleClass;
        }
        return $this;
    }

    /**
     * @return boolean
     */
    public function isSwitchTitleLabel()
    {
        return $this->switchTitleLabel;
    }

    /**
     * @param bool $switchTitleLabel
     * @return $this
     */
    public function setSwitchTitleLabel($switchTitleLabel = true)
    {
        $this->switchTitleLabel = $switchTitleLabel;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isWithoutMandatoryStar()
    {
        return $this->withoutMandatoryStar;
    }

    /**
     * @param bool $withoutMandatoryStar
     * @return $this
     */
    public function setWithoutMandatoryStar($withoutMandatoryStar = true)
    {
        $this->withoutMandatoryStar = $withoutMandatoryStar;
        return $this;
    }

    /**
     * @return string
     */
    public function getSpecialMandatoryMessage()
    {
        return $this->specialMandatoryMessage;
    }

    /**
     * @param $specialMandatoryMessage
     * @return $this
     */
    public function setSpecialMandatoryMessage($specialMandatoryMessage)
    {
        $this->specialMandatoryMessage = $specialMandatoryMessage;
        return $this;
    }

    /**
     * @return string
     */
    public function getTestValue()
    {
        return $this->testValue;
    }

    /**
     * @param $testValue
     * @return $this
     */
    public function setTestValue($testValue)
    {
        $this->testValue = $testValue;
        return $this;
    }

    /**
     * @return bool
     */
    public function isIgnoreViewType()
    {
        return $this->ignoreViewType;
    }

    /**
     * @param bool $ignoreViewType
     * @return $this
     */
    public function setIgnoreViewType($ignoreViewType = true)
    {
        $this->ignoreViewType = $ignoreViewType;
        return $this;
    }

    /**
     * @return string
     */
    public function getAlign()
    {
        return $this->align;
    }

    /**
     * @param $align
     * @return $this
     */
    public function setAlign($align)
    {
        $this->align = $align;
        return $this;
    }

    /**
     * @return bool
     */
    public function isConditionalDisplay()
    {
        return $this->conditionalDisplay;
    }

    /**
     * @param bool $conditionalDisplay
     * @return $this
     */
    public function setConditionalDisplay($conditionalDisplay = true)
    {
        $this->conditionalDisplay = $conditionalDisplay;
        return $this;
    }

    /**
     * @return null
     */
    public function getConditionalFieldName()
    {
        return $this->conditionalFieldName;
    }

    /**
     * @param $conditionalFieldName
     * @return $this
     */
    public function setConditionalFieldName($conditionalFieldName)
    {
        $this->conditionalFieldName = $conditionalFieldName;
        return $this;
    }

    /**
     * @return null
     */
    public function getDisplayValue()
    {
        return $this->displayValue;
    }

    /**
     * @param $displayValue
     * @return $this
     */
    public function setDisplayValue($displayValue)
    {
        $this->displayValue = $displayValue;
        return $this;
    }

    /**
     * @return string
     */
    public function getAddStrBeforeValue()
    {
        return $this->addStrBeforeValue;
    }

    /**
     * @param $addStrBeforeValue
     * @return $this
     */
    public function setAddStrBeforeValue($addStrBeforeValue)
    {
        $this->addStrBeforeValue = $addStrBeforeValue;
        return $this;
    }

    /**
     * @return string
     */
    public function getAddStrBehindValue()
    {
        return $this->addStrBehindValue;
    }

    /**
     * @param $addStrBehindValue
     * @return $this
     */
    public function setAddStrBehindValue($addStrBehindValue)
    {
        $this->addStrBehindValue = $addStrBehindValue;
        return $this;
    }

    /**
     * @return bool
     */
    public function isDisplay()
    {
        return $this->display;
    }

    /**
     * @param bool $display
     * @return $this
     */
    public function setDisplay($display = true)
    {
        $this->display = $display;
        return $this;
    }

    /**
     * @return bool
     */
    public function isRemoveWithEmptyCondition()
    {
        return $this->removeWithEmptyCondition;
    }

    /**
     * @param bool $removeWithEmptyCondition
     * @return $this
     */
    public function setRemoveWithEmptyCondition($removeWithEmptyCondition = true)
    {
        $this->removeWithEmptyCondition = $removeWithEmptyCondition;
        return $this;
    }

    /**
     * @return bool
     */
    public function isTileClass()
    {
        return $this->tileClass;
    }

    /**
     * @param bool $tileClass
     * @return $this
     */
    public function setTileClass($tileClass = true)
    {
        $this->tileClass = $tileClass;
        return $this;
    }

    /**
     * @return string
     */
    public function getTileClassTable()
    {
        return $this->tileClassTable;
    }

    /**
     * @param $tileClassTable
     * @return $this
     */
    public function setTileClassTable($tileClassTable)
    {
        $this->tileClassTable = $tileClassTable;
        return $this;
    }

    /**
     * @return string
     */
    public function getTileClassField()
    {
        return $this->tileClassField;
    }

    /**
     * @param $tileClassField
     * @return $this
     */
    public function setTileClassField($tileClassField)
    {
        $this->tileClassField = $tileClassField;
        return $this;
    }

    /**
     * @return bool
     */
    public function isShowSum()
    {
        return $this->showSum;
    }

    /**
     * @param bool $showSum
     * @return $this
     */
    public function setShowSum($showSum = true)
    {
        $this->showSum = $showSum;
        return $this;
    }

    /**
     * @return bool
     */
    public function isInitInvisible()
    {
        return $this->initInvisible;
    }

    /**
     * @param bool $invisible
     * @return $this
     */
    public function setInitInvisible($invisible = true)
    {
        $this->initInvisible = $invisible;
        return $this;
    }

    /**
     * @return string
     */
    public function getRandomValue()
    {
        return $this->randomValue;
    }

    /**
     * @param string $randomValue
     * @param string $caption The value shown in the FormField where the value normally would be if no value has been stored in the Database yet.
     * @return $this
     */
    public function setRandomValue($randomValue, $caption = '')
    {
        $this->randomValue = $randomValue;
        $this->initialValue = $caption;
        return $this;
    }

    /**
     * @return int
     */
    public function getTableColumnPriority()
    {
        return $this->tableColumnPriority;
    }

    /**
     * @param $tableColumnPriority
     * @return $this
     */
    public function setTableColumnPriority($tableColumnPriority)
    {
        $this->tableColumnPriority = $tableColumnPriority;
        return $this;
    }

    /**
     * @return bool
     */
    public function isPopupField()
    {
        return $this->popupField;
    }

    /**
     * @param $popupField
     * @return $this
     */
    public function setPopupField($popupField)
    {
        $this->popupField = $popupField;
        return $this;
    }

    /**
     * @return string
     */
    public function getAdditionalLabel()
    {
        return $this->additionalLabel;
    }

    /**
     * @param string $additionalLabel
     */
    public function setAdditionalLabel($additionalLabel)
    {
        $this->additionalLabel = $additionalLabel;
    }


}