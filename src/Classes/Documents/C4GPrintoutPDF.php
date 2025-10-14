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
namespace con4gis\ProjectsBundle\Classes\Documents;

use con4gis\CoreBundle\Resources\contao\models\C4gLogModel;
use con4gis\DocumentsBundle\Classes\Stack\PdfManager;
use con4gis\ProjectsBundle\Classes\Common\C4GBrickConst;
use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialog;
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GDateField;
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GEmailField;
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GForeignArrayField;
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GGridField;
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GImageField;
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GMultiCheckboxField;
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GPostalField;
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GRadioGroupField;
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GSelectField;
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GSubDialogField;
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GTelField;
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GTextareaField;
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GTextField;
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GTimeField;
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GTimepickerField;
use Contao\StringUtil;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;

class C4GPrintoutPDF
{
    private $framework;
    private $database;
    private $language;
    private $headline = '';

    /**
     * C4GPrintoutPDF constructor.
     */
    public function __construct($database, $language = '')
    {
        $this->database = $database;
        $this->language = $language ?: $GLOBALS['TL_LANGUAGE'];
    }

    private function checkSubFields(&$field, $data)
    {
        if ($field instanceof C4GSubDialogField) {
            $subFieldList = [];
            if ($field->getFieldList()) {
                foreach ($field->getFieldList() as $subField) {
                    $subField->setDescription('');
                    $subField->setEditable(false);
                    $subField->setShowIfEmpty(false);
                    if (($subField instanceof C4GTextField) || ($subField instanceof C4GTextareaField)) {
                        $newField = new C4GTextField();
                        $newField->setFieldName($subField->getFieldName());
                        $newField->setTitle($subField->getTitle());
                        $newField->setSimpleTextWithoutEditing(true);
                        $newField->setShowIfEmpty(false);
                        $newField->setPrintable($subField->isPrintable());
                        $newField->setPrintableTableRow(true);
                        $subField = $newField;
                    }
                    if ($subField instanceof C4GSelectField) {
                        $subField->setSimpleTextWithoutEditing(true);
                        $subField->setInitialValue($data[$field->getFieldName()]);
                        $subField->setPrintableTableRow(true);
                    }
                    if ($subField->isPrintable() && (trim($data[$subField->getFieldName()]) || (($field instanceof C4GSubDialogField) || ($field instanceof C4GForeignArrayField) || ($field instanceof C4GGridField)))) {
                        $resultField = C4GPrintoutPDF::checkSubFields($subField, $data);
                        $subFieldList[] =  $resultField ?: $subField;
                    }
                }
                $field->setFieldList($subFieldList);
            }
        }

        if ($field instanceof C4GForeignArrayField) {
            $subFieldList = [];
            if ($field->getForeignFieldList()) {
                foreach ($field->getForeignFieldList() as $subField) {
                    $subField->setDescription('');
                    $subField->setEditable(false);
                    $subField->setShowIfEmpty(false);

                    if (($subField instanceof C4GTextField) || ($subField instanceof C4GTextareaField)) {
                        $newField = new C4GTextField();
                        $newField->setFieldName($subField->getFieldName());
                        $newField->setTitle($subField->getTitle());
                        $newField->setSimpleTextWithoutEditing(true);
                        $newField->setShowIfEmpty(false);
                        $newField->setPrintable($subField->isPrintable());
                        $newField->setPrintableTableRow(true);
                        $subField = $newField;
                    }
                    if ($subField instanceof C4GSelectField) {
                        $subField->setSimpleTextWithoutEditing(true);
                        $subField->setInitialValue($data[$field->getFieldName()]);
                        $subField->setPrintableTableRow(true);
                    }
                    if ($subField->isPrintable() && (trim($data[$subField->getFieldName()]) || (($field instanceof C4GSubDialogField) || ($field instanceof C4GForeignArrayField) || ($field instanceof C4GGridField)))) {
                        $resultField = C4GPrintoutPDF::checkSubFields($subField, $data);
                        $subFieldList[] = $resultField ?: $subField;
                    }
                }
                $field->setForeignFieldList($subFieldList);
            }
        }

        if ($field instanceof C4GGridField) {
            $grid = $field->getGrid();
            if ($grid) {
                $newField = new C4GTextField();
                $newField->setFieldName($field->getFieldName());
                $newField->setTitle($field->getTitle());
                $newField->setSimpleTextWithoutEditing(true);
                $newField->setShowIfEmpty(false);
                $newField->setPrintable($field->isPrintable());
                $newField->setPrintableTableRow(true);
                $newField->setInitialValue($field->getInitialValue());
                foreach ($grid->getElements() as $element) {
                    $subField = $element->getField();
                    if ((($subField instanceof C4GTextField) || ($subField instanceof C4GTextareaField) || ($subField instanceof C4GPostalField)) && (trim($data[$subField->getFieldName()]))) {
                        $value =  trim($data[$subField->getFieldName()]);
                        $newField->setInitialValue($newField->getInitialValue() ? $newField->getInitialValue() . ' ' .  $value : $value);
                    }
                }

                return $newField;
            }
        }

        return false;
    }

    public function printAction($module, $data, $id, $buttonClick = false)
    {
        $rootDir = \Contao\System::getContainer()->getParameter('kernel.project_dir');
        $pass = '';
        if (method_exists($module, 'printPdf')) {
            return $module->printPdf($id);
        }

        $module->setLanguage($this->language);
        $module->initBrickModule($id);

        $module->getDialogParams()->setTabContent(false);
        $module->getDialogParams()->setAccordion(false);

        $fieldList = $module->getFieldList();
        $printFieldList = [];

        if ($id) {
            $tableName = $module->getBrickDatabase()->getParams()->getTableName();
            if ($tableName) {
                $dataset = $module->getBrickDatabase()->findByPk($id);
                if ($dataset) {
                    $passwordfield = $module->getDialogParams()->getPasswordField();
                    $passwordFormat = $module->getDialogParams()->getPasswordFormat();
                    if ($passwordfield && !($buttonClick && $module->getDialogParams()->isNoPasswordOnButtonClick())) {
                        $pass = $passwordFormat ? date($passwordFormat, $dataset->$passwordfield) : $dataset->$passwordfield;
                    }
                }
            }
        }

        foreach ($fieldList as $field) {
            $field->setDescription('');
            $field->setEditable(false);
            $field->setShowIfEmpty(false);

            if ($field->getCondition()) {
                foreach ($field->getCondition() as $con) {
                    $conFieldName = $con->getFieldName();
                    if (!$con->checkAgainstCondition($data[$conFieldName])) {
                        continue(2);
                    }
                }
            }

            if (
                ($field instanceof C4GTextField) ||
                ($field instanceof C4GTextareaField) ||
                ($field instanceof C4GDateField) ||
                ($field instanceof C4GTimeField) ||
                ($field instanceof C4GTimepickerField) ||
                ($field instanceof C4GEmailField) ||
                ($field instanceof C4GPostalField) ||
                ($field instanceof C4GTelField)) {
                $newField = new C4GTextField();
                $newField->setFieldName($field->getFieldName());
                $newField->setTitle($field->getTitle());
                $newField->setSimpleTextWithoutEditing(true);
                $newField->setShowIfEmpty(false);
                $newField->setPrintable($field->isPrintable());
                $newField->setPrintableTableRow(true);
                $field = $newField;
            }
            if ($field instanceof C4GSelectField) {
                $field->setSimpleTextWithoutEditing(true);
                $field->setInitialValue($data[$field->getFieldName()]);
                $field->setPrintableTableRow(true);
            }

            if ($field instanceof C4GImageField) {
                $fieldName = $field->getFieldName();

                if ($dataset) {
                    $field->setInitialValue($dataset->$fieldName);
                }
            }

            if ($field instanceof C4GRadioGroupField) {
                $fieldName = $field->getFieldName();
                $field->setShowIfEmpty(false);
                $field->setPrintableTableRow(false);
                $field->setCondition([]);
                $field->setRemoveWithEmptyCondition(false);
                if ($field->isSaveAsArray()) {
                    $field->setInitialValue($dataset->$fieldName ? intvaL(StringUtil::deserialize($dataset->$fieldName)[0]) : '');
                } else {
                    $field->setInitialValue($dataset->$fieldName);
                }
                $field->setDefaultValue($field->getInitialValue());
            }

            if ($field->isPrintable() && ($field->getFieldName() && $data[$field->getFieldName()] && (trim($data[$field->getFieldName()])) || (($field instanceof C4GSubDialogField) || ($field instanceof C4GForeignArrayField) || ($field instanceof C4GImageField) || ($field instanceof C4GRadioGroupField) || ($field instanceof C4GGridField)))) {
                $resultField = C4GPrintoutPDF::checkSubFields($field, $data);
                if ($resultField) {
                    $data[$resultField->getFieldName()] = $resultField->getInitialValue();
                }

                $printFieldList[] = $resultField ?: $field;
            }
        }

        $content = C4GBrickDialog::buildDialogView(
            $printFieldList,
            $module->getBrickDatabase(),
            $data,
            null,
            $module->getDialogParams(),
            true
        );

        $pdfManager = new PdfManager(null, null, $pass);

        if ($module->getPrintStyle()) {
            $style = $module->getPrintStyle();
        } else {
            $style = $rootDir . 'bundles/con4gisprojects/dist/css/c4g_brick_print.min.css';
        }
        $pdfManager->style = $style;

        foreach ($data as $key=>$value) {
            if (strpos($value, "\n") !== false) {
                $data[$key] = str_replace("\n", "<br>", $value);
            }
        }

        $pdfData = [];
        $pdfData['template'] = $module->getPrintTemplate();
        $pdfData['filename'] = date('Y_m_d-H_i_s') . rand(100, 999) . '_document.pdf'; //'{{date::Y_m_d-H_i_s}}-'
        $pdfData['filepath'] = C4GBrickConst::PATH_BRICK_DOCUMENTS;
        $pdfData['Attachment'] = false;
        $pdfData['fieldData'] = $data;
        $pdfData['fieldList'] = $printFieldList;
        $pdfData['chroot'] = dirname($style);
        $pdfData['style'] = $style;

        $pdfManager->setData($pdfData);

        $captionField = $module->getDialogParams()->getCaptionField();
        $pdfManager->headline = $this->headline  . ': ' . $data[$captionField] ?: $module->getDialogParams()->getBrickCaption() . ': ' . $data[$captionField];
        $pdfManager->hl = 'h1';

        $pdfManager->content = $content;
        $pdfManager->save();

        $path = $pdfManager->getPdfDocument()->getPath() . $pdfManager->getPdfDocument()->getFilename();
        // cut out the local path before "files"
        $path = substr($path, strpos($path, 'files'));

        $pdfFieldName = $module->getDialogParams()->getSavePrintoutToField();
        if ($pdfFieldName && $path) {
            $objNew = \Contao\Dbafs::addResource($path);
            $fileUuid = $objNew->uuid;
            $fileUuid = StringUtil::deserialize($fileUuid);
            $tableName = $module->getC4GTablePermissionTable();
            if ($id && $tableName) {
                try {
                    $this->database->prepare("UPDATE $tableName SET $pdfFieldName=? WHERE id=?")->execute($fileUuid, $id);
                } catch (Exception $e) {
                    C4gLogModel::addLogEntry($module->name, 'Error on linking printout to database.');
                }
            }
        }

        $response = new JsonResponse([
            'fileUuid' => $fileUuid ? StringUtil::binToUuid($fileUuid) : '',
            'filePath' => $path,
            'fileName' => $pdfManager->getPdfDocument()->getFilename(),
        ]);

        return $response;
    }

    /**
     * @return mixed
     */
    public function getHeadline()
    {
        return $this->headline;
    }

    /**
     * @param mixed $headline
     */
    public function setHeadline($headline): void
    {
        $this->headline = $headline;
    }
}
