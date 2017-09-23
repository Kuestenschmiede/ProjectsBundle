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

namespace con4gis\ProjectsBundle\Classes\Actions;

class C4GPrintDialogAction extends C4GBrickDialogAction
{
    public function run()
    {
        //ToDo output(), also die Darstellung direkt im Browser funktioniert noch nicht.
        //ToDo CSS Formatierung ausschließlich für den Druck implementieren
        //ToDo Spezialkomponenten wie Tabellen, Karten, ... abbilden
        //ToDo Speicherung der Dokumente abhängig vom ViewType in den Mitglieder-/Gruppenverzeichnissen
        //ToDo Verschiedene Schalter implementieren, damit einige Druckparameter über das Modul übersteuert werden könnten.

        $dlgValues = $this->getPutVars();
        $dialogParams = $this->getDialogParams();
        $dialogId = $dialogParams->getId();
        $memberId = $dialogParams->getMemberId();
        $groupId  = $dialogParams->getGroupId();
        $viewType = $dialogParams->getViewType();
        $fieldList = $this->getFieldList();
        $brickDatabase = $this->getBrickDatabase();

        $content = '';
        if (!$dialogParams->getC4gMap()) {
            $result = $this->withMap($this->getFieldList(), $dialogParams->getContentId());
            if ($result) {
                $content = \Controller::replaceInsertTags('{{insert_content::'.$result.'}}');
            }
        } else {
            $content = $dialogParams->getC4gMap();
        }

        $dataset = $brickDatabase->findByPk($dialogId);
        $content = C4GBrickDialog::buildDialogView($fieldList, $brickDatabase, $dataset, $content, $dialogParams);

        $dialogParams->setAccordion(false);
        $dialogParams->setWithDescriptions(false);
        $dialogParams->setTableRows(false); //con4gis_documents kann noch keine Tabellen drucken (dompdf)

        $pdfManager = new PdfManager();
        $pdfManager->template   = 'c4g_pdftemplate';
        $pdfManager->filename   = '{{date::Y.m.d-H.i.s}}_document.pdf';
        $pdfManager->filepath   = C4GBrickConst::PATH_BRICK_DOCUMENTS;
        $pdfManager->headline   = $dialogParams->getBrickCaption();
        $pdfManager->hl         = 'h1';
        $style                  = TL_ROOT.'bundles/con4gisprojects/css/c4g_brick_print.css';
        $pdfManager->style      = $style;
        $pdfManager->content    = $content;
        $pdfManager->output();
    }
}
