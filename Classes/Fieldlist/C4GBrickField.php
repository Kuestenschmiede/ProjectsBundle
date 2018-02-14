<?php
/**
 * con4gis - the gis-kit
 *
 * @version   php 7
 * @package   con4gis
 * @author    con4gis contributors (see "authors.txt")
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2017.
 * @link      https://www.kuestenschmiede.de
 */

namespace con4gis\ProjectsBundle\Classes\Fieldlist;

use con4gis\CoreBundle\Resources\contao\classes\C4GUtils;
use con4gis\ProjectsBundle\Classes\Conditions\C4GBrickConditionType;
use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialogParams;
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GButtonField;
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GDecimalField;
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GEmailField;
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GLinkField;
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GNumberField;
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GUrlField;
use con4gis\ProjectsBundle\Classes\Views\C4GBrickViewType;
use con4gis\CoreBundle\Resources\contao\classes\C4GHTMLFactory;

abstract class C4GBrickField
{
    //Todo globale Properties prüfen und ggf. den Feldklassen zuordnen (z.B. step zum IntField)

    //please follow alphabetical sorting
    protected $action = array(); //action for action callable fieldtypes
    protected $align = 'left';
    protected $additionalID = ''; //additional ID for switchable (hidden) Fields with same fieldname
    protected $addressField = null; //nominatim reverse search
    protected $callOnChange = false; //call function on change (useful with select and other types)
    protected $callOnChangeFunction = 'C4GCallOnChange(this)'; //call this function on change
    protected $columnWidth = 0; //culumn width on datatable view
    protected $comparable = true; //for field Compare on saving
    protected $condition = array(); //see C4GBrickCondition
    protected $contentId = ''; //for transfer contao content elements like Map
    protected $databaseField = true; //is this field with database mapping (saving, compare, ...)
    protected $dbUnique = false; //unique field value
    protected $dbUniqueResult = ''; //Achtung! Der Result Text muss im Modul vergeben werden, da die Abfrage über die ganze Datenbank alle Mitglieder betrifft und das Ergebnis entsprechend sensibel ist.
    protected $dbUniqueAdditionalCondition = ''; //Zusätzliche Bedingungen die in der Query berücksichtigt werden können.
    protected $description = ''; //field description (under input field)
    protected $display = true; // if false, the field is hidden & will not be checked during the mandatory check
    protected $editable = true; //is the field editable?

    //new
    protected $addStrBeforeValue = '';
    protected $addStrBehindValue = '';

    //special properties for external table fields
    protected $externalCallBackFunction = null;
    protected $externalFieldName = null;
    protected $externalIdField = null;
    protected $externalModel = null;
    protected $externalSearchValue = null;
    protected $externalSortField = null;

    protected $extTitleField = null; //Sonderlocke für Feld in Feld zum Beispiel für Feldbewertungen
    protected $fieldName = null; //the fieldName should be conform to the database field name
    protected $fileTypes = ''; //see C4GBrickFileType
    protected $formField = true; //you can use this property. If the field shouldn't part of the formular
    protected $hidden = false; //for hidden fields
    protected $ignoreViewType = false; //special to set over viewType properties like disabled fields
    protected $initialFields = array(); //z.B. für die Bildgallerie
    protected $initialValue = ''; //set the initial value for the field (presetting)
    protected $insertLink = ''; //???
    protected $latitudeField = 'loc_geoy'; //Coordinate for geopicker field
    protected $loadOptions = null; //see C4GBrickLoadOptions
    protected $longitudeField = 'loc_geox'; //Coordinate for geopicker field
    protected $mandatory = false; //mandatory field?
    protected $max = 99999; //max value
    protected $maxLength = 1024; //max length
    protected $min = 0; //min value
    protected $notificationField = false; // for Notification Center
    protected $options = array(); //Options for select boxes, radio groups, ...
    protected $radiusFieldName = ''; //???
    protected $searchField = false; //für C4GMatching
    protected $searchMaximumField = false; // C4GMatching
    protected $searchMinimumField = false; // C4GMatching
    protected $searchWeightings = 1; //für C4GMatching
    protected $showIfEmpty = true; //do not show clear fields?
    protected $size = 0; //size for some types
    protected $step = 1; //step for int fields with interval steps
    protected $sort = true; //activate sorting for fieldtypes with options
    protected $sortColumn = false; //is this a sort column in datatable?
    protected $sortSequence = 'asc'; //sort sequence
    protected $sortType = ''; //sort type de_date, de_datetime
    protected $source = C4GBrickFieldSourceType::DATABASE; //datasource, if not database
    protected $sourceField = null; //field content not database field
    protected $styleClass = ''; //additional css class for this field
    protected $tableColumn = false; //show this field in datatable?
    protected $tableRow = false; //show field as table row -> label, input in one row
    protected $tableRowLabelWidth = '25%'; //table row label width
    protected $tableRowWidth = '98.2%'; //table row width
    protected $testValue = ''; //so use can optional a check value if needed
    protected $title = ''; //field title (label)
    protected $type = '';  //siehe C4GBrickFieldType
    protected $withoutDescriptionLineBreak = false; //no additonal line break behind description
    protected $withEmptyOption = false; //default empty option for fieldtypes with options like select box
    protected $withoutLabel = false; //do not show the label
    protected $unique = false; //??? unique vs. dbunique
    protected $specialMandatoryMessage = ''; //do not show the default mandatory message
    protected $switchTitleLabel = false; //change order label, input
    protected $withoutMandatoryStar = false; //do not show the mandatory star
    protected $conditionalDisplay = false; // set true if field should be hidden dependent on the value of another field
    protected $conditionalFieldName = null; // the fieldname of the field which is the condition
    protected $displayValue = null; // the value of the condition field at which this field will be displayed
    protected $removeWithEmptyCondition = false; // soll das Feld ohne Conditions ausgeblendet werden (valueSwitch)
    protected $showSum = false; // zeigt eine zusätzliche Footer Zeile mit der Spaltensumme.
    protected $tileClass = false; //soll die value als tile class gesetzt werden.
    protected $tileClassTable = ''; // soll der value für die class aus einer Tabelle geholt werden?
    protected $tileClassField = ''; // aus welchem Feld soll der value für die class geholt werden?

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
        if ($title && !$this->withoutLabel || $this instanceof C4GButtonField || $this instanceof C4GLinkField) {
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
            if($this->isWithoutLabel()){
                return $tdo.'<label for="' . $id . '" ' . $condition['conditionPrepare'] . '>' . $star . $linebreak . '</label>'.$tdc.$extTitleField;
            } else {
                return $tdo.'<label for="' . $id . '" ' . $condition['conditionPrepare'] . '>' . $star . $title . $linebreak . '</label>'.$tdc.$extTitleField;
            }
        } else {
            return '';
        }
    }

    protected function checkCondition($fieldList, $data, $conditions)
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
            $result = '<p class="c4g_description" ' . $condition['conditionPrepare'] . '><a href="' . $description . '" target="_blank">Link</a>' . C4GHTMLFactory::lineBreak() . C4GHTMLFactory::lineBreak() . '</p>';
        } else if ($description && ($description != '')) {
            $result = '<p class="c4g_description" ' . $condition['conditionPrepare'] . '>' . $description . C4GHTMLFactory::lineBreak() . C4GHTMLFactory::lineBreak() . '</p>';
        } else if (!$withoutLineBreak) {
            $result = '<p ' . $condition['conditionPrepare'] . '>' . '</p>';
        } else {
            $result = '<div class="c4g_descripton_hole"></div>';
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
            $class = 'class='.$this->getStyleClass().' ';
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
            if ($this->getConditionalFieldName() && isset($this->displayValue)) {
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
        . $condition['conditionDisable'] . '>' .
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
                    return $required;
                }

            }
        }

        $viewType = $dialogParams->getViewType();

        if (!$this->ignoreViewType && $viewType && (
                ($viewType == C4GBrickViewType::PUBLICVIEW) ||
                ($viewType == C4GBrickViewType::GROUPVIEW) ||
                ($viewType == C4GBrickViewType::PROJECTPARENTVIEW) ||
                ($viewType == C4GBrickViewType::MEMBERVIEW) ||
                ($dialogParams->isFrozen() == 1))
        ) {
            $required = "disabled readonly";
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
        if ($this->getAdditionalID()) {
            return "c4g_" . $this->getFieldName() . "_" . $this->getAdditionalID();
        } else {
            return "c4g_" . $this->getFieldName();
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
     * @param string $radiusFieldName
     */
    public function setRadiusFieldName($radiusFieldName)
    {
        $this->radiusFieldName = $radiusFieldName;
    }

    /**
     * @return null
     */
    public function getAddressField()
    {
        return $this->addressField;
    }

    /**
     * @param null $addressField
     */
    public function setAddressField($addressField)
    {
        $this->addressField = $addressField;
    }

    /**
     * @return null
     */
    public function getExternalSortField()
    {
        return $this->externalSortField;
    }

    /**
     * @param null $externalSortField
     */
    public function setExternalSortField($externalSortField)
    {
        $this->externalSortField = $externalSortField;
    }


    /**
     * @return null
     */
    public function getExternalSearchValue()
    {
        return $this->externalSearchValue;
    }

    /**
     * @param null $externalSearchValue
     */
    public function setExternalSearchValue($externalSearchValue)
    {
        $this->externalSearchValue = $externalSearchValue;
    }


    /**
     * @return string
     */
    public function getExternalModel()
    {
        return $this->externalModel;
    }

    /**
     * @param string $ExternalModel
     */
    public function setExternalModel($externalModel)
    {
        $this->externalModel = $externalModel;
    }

    /**
     * @return string
     */
    public function getExternalIdField()
    {
        return $this->externalIdField;
    }

    /**
     * @param string $ExternalIdField
     */
    public function setExternalIdField($externalIdField)
    {
        $this->externalIdField = $externalIdField;
    }

    /**
     * @return null
     */
    public function getExternalFieldName()
    {
        return $this->externalFieldName;
    }

    /**
     * @param null $externalFieldName
     */
    public function setExternalFieldName($externalFieldName)
    {
        $this->externalFieldName = $externalFieldName;
    }

    /**
     * @return string
     */
    public function getMaxLength()
    {
        return $this->maxLength;
    }

    /**
     * @param string $maxLength
     */
    public function setMaxLength($maxLength)
    {
        $this->maxLength = $maxLength;
    }


    private $withLinkDescription = false;

    /**
     * @return string
     */

    /**
     * @return string
     */
    /**
     * @return string
     */
    public function getLatitudeField()
    {
        return $this->latitudeField;
    }

    /**
     * @param string $latitudeField
     */
    public function setLatitudeField($latitudeField)
    {
        $this->latitudeField = $latitudeField;
    }

    /**
     * @return string
     */
    public function getLongitudeField()
    {
        return $this->longitudeField;
    }

    /**
     * @param string $longitudeField
     */
    public function setLongitudeField($longitudeField)
    {
        $this->longitudeField = $longitudeField;
    }

    /**
     * @return boolean
     */
    public function isNotificationField()
    {
        return $this->notificationField;
    }

    /**
     * @param boolean $notificationField
     */
    public function setNotificationField($notificationField)
    {
        $this->notificationField = $notificationField;
    }

    /**
     * @return boolean
     */
    public function isSearchField()
    {
        return $this->searchField;
    }

    /**
     * @param boolean $searchField
     */
    public function setSearchField($searchField)
    {
        $this->searchField = $searchField;
    }

    /**
     * @return boolean
     */
    public function isSearchMinimumField()
    {
        return $this->searchMinimumField;
    }

    /**
     * @param boolean $searchMinimumField
     */
    public function setSearchMinimumField($searchMinimumField)
    {
        $this->searchMinimumField = $searchMinimumField;
    }

    /**
     * @return boolean
     */
    public function isSearchMaximumField()
    {
        return $this->searchMaximumField;
    }

    /**
     * @param boolean $searchMaximumField
     */
    public function setSearchMaximumField($searchMaximumField)
    {
        $this->searchMaximumField = $searchMaximumField;
    }


    /**
     * @return int
     */
    public function getSearchWeightings()
    {
        return $this->searchWeightings;
    }

    /**
     * @param int $searchWeightings
     */
    public function setSearchWeightings($searchWeightings)
    {
        $this->searchWeightings = $searchWeightings;
    }



    /**
     * @return string
     */
    public function getSourceField()
    {
        return $this->sourceField;
    }

    /**
     * @param string $sourcefield
     */
    public function setSourceField($sourceField)
    {
        $this->sourceField = $sourceField;
    }

    /**
     * @return boolean
     */
    public function isWithLinkDescription()
    {
        return $this->withLinkDescription;
    }

    /**
     * @param boolean $withLinkDescription
     */
    public function setWithLinkDescription($withLinkDescription)
    {
        $this->withLinkDescription = $withLinkDescription;
    }

    /**
     * @return boolean
     */
    public function isWithEmptyOption()
    {
        return $this->withEmptyOption;
    }

    /**
     * @param boolean $withEmptyOption
     */
    public function setWithEmptyOption($withEmptyOption)
    {
        $this->withEmptyOption = $withEmptyOption;
    }


    public function setAdditionalID($additionalID)
    {
        $this->additionalID = $additionalID;
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
     * @param string $insertLink
     */
    public function setInsertLink($insertLink)
    {
        $this->insertLink = $insertLink;
    } // Like {{link_url::??}}


    /**
     * @return array
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param array $action
     */
    public function setAction($actionType, $actionEvent)
    {
        $this->action = array('actiontype' => $actionType, 'actionevent' => $actionEvent);
    } //z.B. für die Bildgallerie



    /**
     * @return boolean
     */
    public function isComparable()
    {
        return $this->comparable;
    }

    /**
     * @param boolean $comparable
     */
    public function setComparable($comparable)
    {
        $this->comparable = $comparable;
    }


    /**
     * @return boolean
     */
    public function isShowIfEmpty()
    {
        return $this->showIfEmpty;
    }

    /**
     * @param boolean $showIfEmpty
     */
    public function setShowIfEmpty($showIfEmpty)
    {
        $this->showIfEmpty = $showIfEmpty;
    }

    /**
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param string $source
     */
    public function setSource($source)
    {
        $this->source = $source;
    }



    /**
     * @return null
     */
    public function getLoadOptions()
    {
        return $this->loadOptions;
    }

    /**
     * @param null $loadOptions
     */
    public function setLoadOptions($loadOptions)
    {
        $this->loadOptions = $loadOptions;
    }

    /**
     * @return array
     */
    public function getInitialFields()
    {
        return $this->initialFields;
    }

    /**
     * @param array $initialFields
     */
    public function setInitialFields($initialFields)
    {
        $this->initialFields = $initialFields;
    } //siehe C4GBrickConditionType

    /**
     * @param string $conditionType
     */
    public function setConditionType($conditionType)
    {
        $this->conditionType = $conditionType;
    }

    /**
     * @param string $conditionType
     */
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
     * @param null $condition
     */
    public function setCondition($condition)
    {
        if (is_array($condition)) {
            $this->condition = $condition;
        } else {
            $this->condition[] = $condition;
        }
    }


    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    } //siehe C4GBrickFileType


    /**
     * @return string
     */
    public function getFileTypes()
    {
        return $this->fileTypes;
    }

    /**
     * @param string $fileTypes
     */
    public function setFileTypes($fileTypes)
    {
        $this->fileTypes = $fileTypes;
    }

    /**
     * @return string
     */
    public function getContentId()
    {
        return $this->contentId;
    }

    /**
     * @param string $contentId
     */
    public function setContentId($contentId)
    {
        $this->contentId = $contentId;
    } //zur Einbindung von Inhaltselementen in die Maske


    /**
     * @return boolean
     */
    public function isDatabaseField()
    {
        return $this->databaseField;
    }

    /**
     * @param boolean $databaseField
     */
    public function setDatabaseField($databaseField)
    {
        $this->databaseField = $databaseField;
    }

    /**
     * @return null
     */
    public function getInitialValue()
    {
        return $this->initialValue;
    }

    /**
     * @param null $initialValue
     */
    public function setInitialValue($initialValue)
    {
        $this->initialValue = $initialValue;
    }

    /**
     * @return string
     */
    public function getSortSequence()
    {
        return $this->sortSequence;
    }

    /**
     * @param string $sortSequence
     */
    public function setSortSequence($sortSequence)
    {
        $this->sortSequence = $sortSequence;
    }

    /**
     * @return string
     */
    public function getSortType()
    {
        return $this->sortType;
    }

    /**
     * @param string $sortType
     */
    public function setSortType($sortType)
    {
        $this->sortType = $sortType;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param array $options
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }

    /**
     * @param $model
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
    }



    /**
     * @return boolean
     */
    public function isFormField()
    {
        return $this->formField;
    }

    /**
     * @param boolean $formField
     */
    public function setFormField($formField)
    {
        $this->formField = $formField;
    }

    /**
     * @return boolean
     */
    public function isEditable()
    {
        return $this->editable;
    }

    /**
     * @param boolean $editable
     */
    public function setEditable($editable)
    {
        $this->editable = $editable;
    }

    /**
     * @return boolean
     */
    public function isMandatory()
    {
        return $this->mandatory;
    }

    /**
     * @param boolean $mandatory
     */
    public function setMandatory($mandatory)
    {
        $this->mandatory = $mandatory;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param int $size
     */
    public function setSize($size)
    {
        $this->size = $size;
    }

    /**
     * @return int
     */
    public function getMin()
    {
        return $this->min;
    }

    /**
     * @param int $min
     */
    public function setMin($min)
    {
        $this->min = $min;
    }

    /**
     * @return int
     */
    public function getMax()
    {
        return $this->max;
    }

    /**
     * @param int $max
     */
    public function setMax($max)
    {
        $this->max = $max;
    }

    /**
     * @return int
     */
    public function getStep()
    {
        return $this->step;
    }

    /**
     * @param int $step
     */
    public function setStep($step)
    {
        $this->step = $step;
    }


    /**
     * @return null
     */
    public function getFieldName()
    {
        return $this->fieldName;
    }

    /**
     * @param null $thisName
     */
    public function setFieldName($thisName)
    {
        $this->fieldName = $thisName;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        /*if ($this->isMandatory())
            return '*'.$this->title;
        else*/
            return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param int $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return null
     */
    public function getExternalCallBackFunction()
    {
        return $this->externalCallBackFunction;
    }

    /**
     * @param null $externalCallBackFunction
     */
    public function setExternalCallBackFunction($externalCallBackFunction)
    {
        $this->externalCallBackFunction = $externalCallBackFunction;
    }

    /**
     * @return int
     */
    public function getColumnWidth()
    {
        return $this->columnWidth;
    }

    /**
     * @param int $columnWidth
     */
    public function setColumnWidth($columnWidth)
    {
        $this->columnWidth = $columnWidth;
    }

    /**
     * @return boolean
     */
    public function isSortColumn()
    {
        return $this->sortColumn;
    }

    /**
     * @param boolean $sortColumn
     */
    public function setSortColumn($sortColumn)
    {
        $this->sortColumn = $sortColumn;
    }

    /**
     * @return boolean
     */
    public function isTableColumn()
    {
        return $this->tableColumn;
    }

    /**
     * @param boolean $tableColumn
     */
    public function setTableColumn($tableColumn)
    {
        $this->tableColumn = $tableColumn;
    }

    /**
     * @return boolean
     */
    public function isCallOnChange()
    {
        return $this->callOnChange;
    }

    /**
     * @param boolean $callOnChange
     */
    public function setCallOnChange($callOnChange)
    {
        $this->callOnChange = $callOnChange;
    }

    /**
     * @return string
     */
    public function getCallOnChangeFunction()
    {
        return $this->callOnChangeFunction;
    }

    /**
     * @param string $callOnChangeFunction
     */
    public function setCallOnChangeFunction($callOnChangeFunction)
    {
        $this->callOnChangeFunction = $callOnChangeFunction;
    }

    /**
     * @return boolean
     */
    public function isUnique()
    {
        return $this->unique;
    }

    /**
     * @param boolean $isUnique
     */
    public function setUnique($unique)
    {
        $this->unique = $unique;
    }

    /**
     * @return boolean
     */
    public function isDbUnique()
    {
        return $this->dbUnique;
    }

    /**
     * @param boolean $dbUnique
     */
    public function setDbUnique($dbUnique)
    {
        $this->dbUnique = $dbUnique;
    }

    /**
     * @return string
     */
    public function getDbUniqueResult()
    {
        return $this->dbUniqueResult;
    }

    /**
     * @param string $dbUniqueResult
     */
    public function setDbUniqueResult($dbUniqueResult)
    {
        $this->dbUniqueResult = $dbUniqueResult;
    }

    /**
     * @return string
     */
    public function getDbUniqueAdditionalCondition()
    {
        return $this->dbUniqueAdditionalCondition;
    }

    /**
     * @param string $dbUniqueAdditionalCondition
     */
    public function setDbUniqueAdditionalCondition($dbUniqueAdditionalCondition)
    {
        $this->dbUniqueAdditionalCondition = $dbUniqueAdditionalCondition;
    }

    /**
     * @return boolean
     */
    public function isTableRow()
    {
        return $this->tableRow;
    }

    /**
     * @param boolean $tableRow
     */
    public function setTableRow($tableRow)
    {
        $this->tableRow = $tableRow;
    }

    /**
     * @return string
     */
    public function getTableRowWidth()
    {
        return $this->tableRowWidth;
    }

    /**
     * @param string $tableRowWidth
     */
    public function setTableRowWidth($tableRowWidth)
    {
        $this->tableRowWidth = $tableRowWidth;
    }

    /**
     * @return string
     */
    public function getTableRowLabelWidth()
    {
        return $this->tableRowLabelWidth;
    }

    /**
     * @param string $tableRowLabelWidth
     */
    public function setTableRowLabelWidth($tableRowLabelWidth)
    {
        $this->tableRowLabelWidth = $tableRowLabelWidth;
    }

    /**
     * @return boolean
     */
    public function isSort()
    {
        return $this->sort;
    }

    /**
     * @param boolean $sort
     */
    public function setSort($sort)
    {
        $this->sort = $sort;
    }

    /**
     * @return null
     */
    public function getExtTitleField()
    {
        return $this->extTitleField;
    }

    /**
     * @param null $extTitleField
     */
    public function setExtTitleField($extTitleField)
    {
        $this->extTitleField = $extTitleField;
    }

    /**
     * @return boolean
     */
    public function isHidden()
    {
        return $this->hidden;
    }

    /**
     * @param boolean $hidden
     */
    public function setHidden($hidden)
    {
        $this->hidden = $hidden;
    }

    /**
     * @return boolean
     */
    public function isWithoutLabel()
    {
        return $this->withoutLabel;
    }

    /**
     * @param boolean $withoutLabel
     */
    public function setWithoutLabel($withoutLabel)
    {
        $this->withoutLabel = $withoutLabel;
    }

    /**
     * @return boolean
     */
    public function isWithoutDescriptionLineBreak()
    {
        return $this->withoutDescriptionLineBreak;
    }

    /**
     * @param boolean $withoutDescriptionLineBreak
     */
    public function setWithoutDescriptionLineBreak($withoutDescriptionLineBreak)
    {
        $this->withoutDescriptionLineBreak = $withoutDescriptionLineBreak;
    }

    /**
     * @return string
     */
    public function getStyleClass()
    {
        return $this->styleClass;
    }

    /**
     * @param string $styleClass
     */
    public function setStyleClass($styleClass)
    {
        $this->styleClass = $styleClass;
    }

    /**
     * @return boolean
     */
    public function isSwitchTitleLabel()
    {
        return $this->switchTitleLabel;
    }

    /**
     * @param boolean $switchTitleLabel
     */
    public function setSwitchTitleLabel($switchTitleLabel)
    {
        $this->switchTitleLabel = $switchTitleLabel;
    }

    /**
     * @return boolean
     */
    public function isWithoutMandatoryStar()
    {
        return $this->withoutMandatoryStar;
    }

    /**
     * @param boolean $withoutMandatoryStar
     */
    public function setWithoutMandatoryStar($withoutMandatoryStar)
    {
        $this->withoutMandatoryStar = $withoutMandatoryStar;
    }

    /**
     * @return string
     */
    public function getSpecialMandatoryMessage()
    {
        return $this->specialMandatoryMessage;
    }

    /**
     * @param string $specialMandatoryMessage
     */
    public function setSpecialMandatoryMessage($specialMandatoryMessage)
    {
        $this->specialMandatoryMessage = $specialMandatoryMessage;
    }

    /**
     * @return string
     */
    public function getTestValue()
    {
        return $this->testValue;
    }

    /**
     * @param string $testValue
     */
    public function setTestValue($testValue)
    {
        $this->testValue = $testValue;
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
     */
    public function setIgnoreViewType($ignoreViewType)
    {
        $this->ignoreViewType = $ignoreViewType;
    }

    /**
     * @return string
     */
    public function getAlign()
    {
        return $this->align;
    }

    /**
     * @param string $align
     */
    public function setAlign($align)
    {
        $this->align = $align;
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
     */
    public function setConditionalDisplay($conditionalDisplay)
    {
        $this->conditionalDisplay = $conditionalDisplay;
    }

    /**
     * @return null
     */
    public function getConditionalFieldName()
    {
        return $this->conditionalFieldName;
    }

    /**
     * @param null $conditionalFieldName
     */
    public function setConditionalFieldName($conditionalFieldName)
    {
        $this->conditionalFieldName = $conditionalFieldName;
    }

    /**
     * @return null
     */
    public function getDisplayValue()
    {
        return $this->displayValue;
    }

    /**
     * @param null $displayValue
     */
    public function setDisplayValue($displayValue)
    {
        $this->displayValue = $displayValue;
    }

    /**
     * @return string
     */
    public function getAddStrBeforeValue()
    {
        return $this->addStrBeforeValue;
    }

    /**
     * @param string $addStrBeforeValue
     */
    public function setAddStrBeforeValue($addStrBeforeValue)
    {
        $this->addStrBeforeValue = $addStrBeforeValue;
    }

    /**
     * @return string
     */
    public function getAddStrBehindValue()
    {
        return $this->addStrBehindValue;
    }

    /**
     * @param string $addStrBehindValue
     */
    public function setAddStrBehindValue($addStrBehindValue)
    {
        $this->addStrBehindValue = $addStrBehindValue;
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
     */
    public function setDisplay($display)
    {
        $this->display = $display;
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
     */
    public function setRemoveWithEmptyCondition($removeWithEmptyCondition)
    {
        $this->removeWithEmptyCondition = $removeWithEmptyCondition;
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
     */
    public function setTileClass($tileClass)
    {
        $this->tileClass = $tileClass;
    }

    /**
     * @return string
     */
    public function getTileClassTable()
    {
        return $this->tileClassTable;
    }

    /**
     * @param string $tileClassTable
     */
    public function setTileClassTable($tileClassTable)
    {
        $this->tileClassTable = $tileClassTable;
    }

    /**
     * @return string
     */
    public function getTileClassField()
    {
        return $this->tileClassField;
    }

    /**
     * @param string $tileClassField
     */
    public function setTileClassField($tileClassField)
    {
        $this->tileClassField = $tileClassField;
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
     */
    public function setShowSum($showSum)
    {
        $this->showSum = $showSum;
    }


}