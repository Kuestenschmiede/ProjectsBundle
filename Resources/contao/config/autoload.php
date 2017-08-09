<?php

/**
 * con4gis - the gis-kit
 *
 * @version   php 5
 * @package   con4gis
 * @author    con4gis contributors (see "authors.txt")
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2017.
 * @link      https://www.kuestenschmiede.de
 */

//
///**
// * Register the namespaces
// */
ClassLoader::addNamespaces(array
(
	'\c4g\projects',
    '\c4g\projects\database'
));
//
//
///**
// * Register the classes
// */
ClassLoader::addClasses(array
(
//    /**
//     * Common classes
//     */
//    'c4g\projects\C4GBrickCommon'           => 'system/modules/con4gis_projects/classes/common/C4GBrickCommon.php',
//    'c4g\projects\C4GBrickConst'            => 'system/modules/con4gis_projects/classes/common/C4GBrickConst.php',
//
//    /**
//     * Database classes
//     */
//    'c4g\projects\C4GBrickDatabase'         => 'system/modules/con4gis_projects/classes/database/C4GBrickDatabase.php',
//    'c4g\projects\C4GBrickDatabaseParams'   => 'system/modules/con4gis_projects/classes/database/C4GBrickDatabaseParams.php',
//    'c4g\projects\C4GBrickDatabaseType'     => 'system/modules/con4gis_projects/classes/database/C4GBrickDatabaseType.php',
//    'c4g\projects\C4GBrickEntity'           => 'system/modules/con4gis_projects/classes/database/C4GBrickEntity.php',
//    'c4g\projects\C4GBrickEntitySerializer' => 'system/modules/con4gis_projects/classes/database/C4GBrickEntitySerializer.php',
//
//    /**
//     * Framework classes
//     */
//    'c4g\projects\C4GContainer'             => 'system/modules/con4gis_projects/classes/framework/C4GContainer.php',
//
//    /**
//     * Button classes
//     */
//    'c4g\projects\C4GBrickButton'           => 'system/modules/con4gis_projects/classes/buttons/C4GBrickButton.php',
//
//    /**
//     * Dialog classes
//     */
//	'c4g\projects\C4GBrickDialog'           => 'system/modules/con4gis_projects/classes/dialogs/C4GBrickDialog.php',
//	'c4g\projects\C4GBrickDialogParams'     => 'system/modules/con4gis_projects/classes/dialogs/C4GBrickDialogParams.php',
//    'c4g\projects\C4GBrickGrid'             => 'system/modules/con4gis_projects/classes/dialogs/C4GBrickGrid.php',
//    'c4g\projects\C4GBrickGridElement'      => 'system/modules/con4gis_projects/classes/dialogs/C4GBrickGridElement.php',
//    'c4g\projects\C4GBrickOverlay'          => 'system/modules/con4gis_projects/classes/dialogs/C4GBrickOverlay.php',
//    'c4g\projects\C4GBrickFilterDialog'     => 'system/modules/con4gis_projects/classes/dialogs/C4GBrickFilterDialog.php',
//    'c4g\projects\C4GBrickSelectGroupDialog'   => 'system/modules/con4gis_projects/classes/dialogs/C4GBrickSelectGroupDialog.php',
//    'c4g\projects\C4GBrickSelectProjectDialog' => 'system/modules/con4gis_projects/classes/dialogs/C4GBrickSelectProjectDialog.php',
//    'c4g\projects\C4GBrickSelectParentDialog'  => 'system/modules/con4gis_projects/classes/dialogs/C4GBrickSelectParentDialog.php',
//    'c4g\projects\C4GBeforeDialogSave'      => 'system/modules/con4gis_projects/classes/dialogs/C4GBeforeDialogSave.php',
//
//    /**
//     * Fieldlist classes
//     */
//	'c4g\projects\C4GBrickField'            => 'system/modules/con4gis_projects/classes/fieldlist/C4GBrickField.php',
//	'c4g\projects\C4GBrickFieldType'        => 'system/modules/con4gis_projects/classes/fieldlist/C4GBrickFieldType.php',
//    'c4g\projects\C4GBrickFieldSourceType'  => 'system/modules/con4gis_projects/classes/fieldlist/C4GBrickFieldSourceType.php',
//    'c4g\projects\C4GBrickFieldCompare'     => 'system/modules/con4gis_projects/classes/fieldlist/C4GBrickFieldCompare.php',
//    'c4g\projects\C4GBrickLoadOptions'      => 'system/modules/con4gis_projects/classes/fieldlist/C4GBrickLoadOptions.php',
//
//    /**
//     * Filter classes
//     */
//    'c4g\projects\C4GBrickFilterParams'     => 'system/modules/con4gis_projects/classes/filter/C4GBrickFilterParams.php',
//    'c4g\projects\C4GBrickMatching'         => 'system/modules/con4gis_projects/classes/filter/C4GBrickMatching.php',
//
//    /**
//     * List classes
//     */
//	'c4g\projects\C4GBrickList'             => 'system/modules/con4gis_projects/classes/lists/C4GBrickList.php',
//    'c4g\projects\C4GBrickListParams'       => 'system/modules/con4gis_projects/classes/lists/C4GBrickListParams.php',
//    'c4g\projects\C4GBrickTiles'            => 'system/modules/con4gis_projects/classes/lists/C4GBrickTiles.php',
//    'c4g\projects\C4GBrickRenderMode'       => 'system/modules/con4gis_projects/classes/lists/C4GBrickRenderMode.php',
//
//    /**
//     * Notification classes
//     */
//    'c4g\projects\C4GBrickNotification'     => 'system/modules/con4gis_projects/classes/notifications/C4GBrickNotification.php',
//    'c4g\projects\C4GBrickSendEMail'        => 'system/modules/con4gis_projects/classes/notifications/C4GBrickSendEMail.php',
//
//    /**
//     * Service classes
//     */
//    'c4g\projects\C4GBrickServiceParent'    => 'system/modules/con4gis_projects/classes/services/C4GBrickServiceParent.php',
//
//    /**
//     * View classes
//     */
//	'c4g\projects\C4GBrickView'		        => 'system/modules/con4gis_projects/classes/views/C4GBrickView.php',
//	'c4g\projects\C4GBrickViewType'         => 'system/modules/con4gis_projects/classes/views/C4GBrickViewType.php',
//    'c4g\projects\C4GBrickViewParams'       => 'system/modules/con4gis_projects/classes/views/C4GBrickViewParams.php',
//
//    /**
//     * Condition classes
//     */
//    'c4g\projects\C4GBrickCondition'        => 'system/modules/con4gis_projects/classes/conditions/C4GBrickCondition.php',
//    'c4g\projects\C4GBrickConditionType'    => 'system/modules/con4gis_projects/classes/conditions/C4GBrickConditionType.php',
//
//    /**
//     * File classes
//     */
//    'c4g\projects\C4GBrickFileType'         => 'system/modules/con4gis_projects/classes/files/C4GBrickFileType.php',
//
//    /**
//     * Log classes
//     */
//    'c4g\projects\C4GLogEntryType'          => 'system/modules/con4gis_projects/classes/logs/C4GLogEntryType.php',
//
//    /**
//     * Maps classes
//     */
//	'c4g\projects\C4GBrickMapFrontendParent'=> 'system/modules/con4gis_projects/classes/maps/C4GBrickMapFrontendParent.php',
//	'c4g\projects\C4GCustomEditorTabs'=> 'system/modules/con4gis_projects/classes/maps/C4GCustomEditorTabs.php',
//	'c4g\projects\C4GProjectsFrontend'=> 'system/modules/con4gis_projects/classes/maps/C4GProjectsFrontend.php',
//
//    /**
//     * Action classes
//     */
//    'c4g\projects\C4GBrickAction'           => 'system/modules/con4gis_projects/classes/actions/C4GBrickAction.php',
//    'c4g\projects\C4GBrickActionEvent'      => 'system/modules/con4gis_projects/classes/actions/C4GBrickActionEvent.php',
//    'c4g\projects\C4GBrickActionType'       => 'system/modules/con4gis_projects/classes/actions/C4GBrickActionType.php',
//    'c4g\projects\C4GActivationDialogAction' => 'system/modules/con4gis_projects/classes/actions/C4GActivationDialogAction.php',
//    'c4g\projects\C4GArchiveDialogAction' => 'system/modules/con4gis_projects/classes/actions/C4GArchiveDialogAction.php',
//    'c4g\projects\C4GBrickDialogAction' => 'system/modules/con4gis_projects/classes/actions/C4GBrickDialogAction.php',
//    'c4g\projects\C4GCancelDialogAction' => 'system/modules/con4gis_projects/classes/actions/C4GCancelDialogAction.php',
//    'c4g\projects\C4GCloseDialogAction' => 'system/modules/con4gis_projects/classes/actions/C4GCloseDialogAction.php',
//    'c4g\projects\C4GConfirmActivationAction' => 'system/modules/con4gis_projects/classes/actions/C4GConfirmActivationAction.php',
//    'c4g\projects\C4GConfirmArchiveAction' => 'system/modules/con4gis_projects/classes/actions/C4GConfirmArchiveAction.php',
//    'c4g\projects\C4GConfirmDefrostAction' => 'system/modules/con4gis_projects/classes/actions/C4GConfirmDefrostAction.php',
//    'c4g\projects\C4GConfirmDeleteAction' => 'system/modules/con4gis_projects/classes/actions/C4GConfirmDeleteAction.php',
//    'c4g\projects\C4GConfirmFreezeAction' => 'system/modules/con4gis_projects/classes/actions/C4GConfirmFreezeAction.php',
//    'c4g\projects\C4GConfirmGroupSelectAction' => 'system/modules/con4gis_projects/classes/actions/C4GConfirmGroupSelectAction.php',
//    'c4g\projects\C4GConfirmMessageAction' => 'system/modules/con4gis_projects/classes/actions/C4GConfirmMessageAction.php',
//    'c4g\projects\C4GDefrostDialogAction' => 'system/modules/con4gis_projects/classes/actions/C4GDefrostDialogAction.php',
//    'c4g\projects\C4GDeleteDialogAction' => 'system/modules/con4gis_projects/classes/actions/C4GDeleteDialogAction.php',
//    'c4g\projects\C4GExportDialogAction' => 'system/modules/con4gis_projects/classes/actions/C4GExportDialogAction.php',
//    'c4g\projects\C4GPrintDialogAction' => 'system/modules/con4gis_projects/classes/actions/C4GPrintDialogAction.php',
//    'c4g\projects\C4GFreezeDialogAction' => 'system/modules/con4gis_projects/classes/actions/C4GFreezeDialogAction.php',
//    'c4g\projects\C4GRedirectAction' => 'system/modules/con4gis_projects/classes/actions/C4GRedirectAction.php',
//    'c4g\projects\C4GRedirectBackAction' => 'system/modules/con4gis_projects/classes/actions/C4GRedirectBackAction.php',
//    'c4g\projects\C4GLoginRedirectAction' => 'system/modules/con4gis_projects/classes/actions/C4GLoginRedirectAction.php',
//    'c4g\projects\C4GReloadAction' => 'system/modules/con4gis_projects/classes/actions/C4GReloadAction.php',
//    'c4g\projects\C4GSaveDialogAction' => 'system/modules/con4gis_projects/classes/actions/C4GSaveDialogAction.php',
//    'c4g\projects\C4GSelectGroupDialogAction' => 'system/modules/con4gis_projects/classes/actions/C4GSelectGroupDialogAction.php',
//    'c4g\projects\C4GSelectParentDialogAction' => 'system/modules/con4gis_projects/classes/actions/C4GSelectParentDialogAction.php',
//    'c4g\projects\C4GSelectProjectDialogAction' => 'system/modules/con4gis_projects/classes/actions/C4GSelectProjectDialogAction.php',
//    'c4g\projects\C4GSendEmailAction' => 'system/modules/con4gis_projects/classes/actions/C4GSendEmailAction.php',
//    'c4g\projects\C4GSendNotificationAction' => 'system/modules/con4gis_projects/classes/actions/C4GSendNotificationAction.php',
//    'c4g\projects\C4GSendEmailNotificationAction' => 'system/modules/con4gis_projects/classes/actions/C4GSendEmailNotificationAction.php',
//    'c4g\projects\C4GShowEmailNotificationDialogAction' => 'system/modules/con4gis_projects/classes/actions/C4GShowEmailNotificationDialogAction.php',
//    'c4g\projects\C4GShowFilterDialogAction' => 'system/modules/con4gis_projects/classes/actions/C4GShowFilterDialogAction.php',
//    'c4g\projects\C4GSetFilterAction' => 'system/modules/con4gis_projects/classes/actions/C4GSetFilterAction.php',
//    'c4g\projects\C4GSetParentIdAction' => 'system/modules/con4gis_projects/classes/actions/C4GSetParentIdAction.php',
//    'c4g\projects\C4GSetProjectIdAction' => 'system/modules/con4gis_projects/classes/actions/C4GSetProjectIdAction.php',
//    'c4g\projects\C4GShowDialogAction' => 'system/modules/con4gis_projects/classes/actions/C4GShowDialogAction.php',
//    'c4g\projects\C4GShowListAction' => 'system/modules/con4gis_projects/classes/actions/C4GShowListAction.php',
//    'c4g\projects\C4GShowMessageChangesDialogAction' => 'system/modules/con4gis_projects/classes/actions/C4GShowMessageChangesDialogAction.php',
//    'c4g\projects\C4GShowMessageDialogAction' => 'system/modules/con4gis_projects/classes/actions/C4GShowMessageDialogAction.php',
//    'c4g\projects\C4GChangeFieldAction' => 'system/modules/con4gis_projects/classes/actions/C4GChangeFieldAction.php',
//
//
//    /**
//     * Field classes
//     */
//    'c4g\projects\C4GCheckboxField'             => 'system/modules/con4gis_projects/classes/fieldtypes/C4GCheckboxField.php',
//    'c4g\projects\C4GButtonField'             => 'system/modules/con4gis_projects/classes/fieldtypes/C4GButtonField.php',
//    'c4g\projects\C4GCKEditorField'             => 'system/modules/con4gis_projects/classes/fieldtypes/C4GCKEditorField.php',
//    'c4g\projects\C4GColorField'             => 'system/modules/con4gis_projects/classes/fieldtypes/C4GColorField.php',
//    'c4g\projects\C4GDateField'             => 'system/modules/con4gis_projects/classes/fieldtypes/C4GDateField.php',
//    'c4g\projects\C4GDateTimePickerField'   => 'system/modules/con4gis_projects/classes/fieldtypes/C4GDateTimePickerField.php',
//    'c4g\projects\C4GDateTimeLocationField'             => 'system/modules/con4gis_projects/classes/fieldtypes/C4GDateTimeLocationField.php',
//    'c4g\projects\C4GEmailField'             => 'system/modules/con4gis_projects/classes/fieldtypes/C4GEmailField.php',
//    'c4g\projects\C4GFileField'             => 'system/modules/con4gis_projects/classes/fieldtypes/C4GFileField.php',
//    'c4g\projects\C4GDecimalField'             => 'system/modules/con4gis_projects/classes/fieldtypes/C4GDecimalField.php',
//    'c4g\projects\C4GGalleryField'             => 'system/modules/con4gis_projects/classes/fieldtypes/C4GGalleryField.php',
//    'c4g\projects\C4GGeopickerField'             => 'system/modules/con4gis_projects/classes/fieldtypes/C4GGeopickerField.php',
//    'c4g\projects\C4GGridField'             => 'system/modules/con4gis_projects/classes/fieldtypes/C4GGridField.php',
//    'c4g\projects\C4GHeadlineField'             => 'system/modules/con4gis_projects/classes/fieldtypes/C4GHeadlineField.php',
//    'c4g\projects\C4GImageField'             => 'system/modules/con4gis_projects/classes/fieldtypes/C4GImageField.php',
//    'c4g\projects\C4GInfoTextField'          => 'system/modules/con4gis_projects/classes/fieldtypes/C4GInfoTextField.php',
//    'c4g\projects\C4GNumberField'             => 'system/modules/con4gis_projects/classes/fieldtypes/C4GNumberField.php',
//    'c4g\projects\C4GKeyField'             => 'system/modules/con4gis_projects/classes/fieldtypes/C4GKeyField.php',
//    'c4g\projects\C4GLabelField'          => 'system/modules/con4gis_projects/classes/fieldtypes/C4GLabelField.php',
//    'c4g\projects\C4GLinkField'             => 'system/modules/con4gis_projects/classes/fieldtypes/C4GLinkField.php',
//    'c4g\projects\C4GMultiCheckboxField'             => 'system/modules/con4gis_projects/classes/fieldtypes/C4GMultiCheckboxField.php',
//    'c4g\projects\C4GMultiSelectField'             => 'system/modules/con4gis_projects/classes/fieldtypes/C4GMultiSelectField.php',
//    'c4g\projects\C4GNominatimAddressField'             => 'system/modules/con4gis_projects/classes/fieldtypes/C4GNominatimAddressField.php',
//    'c4g\projects\C4GPermalinkField'             => 'system/modules/con4gis_projects/classes/fieldtypes/C4GPermalinkField.php',
//    'c4g\projects\C4GPostalField'             => 'system/modules/con4gis_projects/classes/fieldtypes/C4GPostalField.php',
//    'c4g\projects\C4GRadioGroupField'             => 'system/modules/con4gis_projects/classes/fieldtypes/C4GRadioGroupField.php',
//    'c4g\projects\C4GRangeField'             => 'system/modules/con4gis_projects/classes/fieldtypes/C4GRangeField.php',
//    'c4g\projects\C4GSelectField'             => 'system/modules/con4gis_projects/classes/fieldtypes/C4GSelectField.php',
//    'c4g\projects\C4GStopwatchField'             => 'system/modules/con4gis_projects/classes/fieldtypes/C4GStopwatchField.php',
//    'c4g\projects\C4GTabButtonPanelField' => 'system/modules/con4gis_projects/classes/fieldtypes/C4GTabButtonPanelField.php',
//    'c4g\projects\C4GTelField'             => 'system/modules/con4gis_projects/classes/fieldtypes/C4GTelField.php',
//    'c4g\projects\C4GTextareaField'             => 'system/modules/con4gis_projects/classes/fieldtypes/C4GTextareaField.php',
//    'c4g\projects\C4GTextField'             => 'system/modules/con4gis_projects/classes/fieldtypes/C4GTextField.php',
//    'c4g\projects\C4GTimeField'             => 'system/modules/con4gis_projects/classes/fieldtypes/C4GTimeField.php',
//    'c4g\projects\C4GTimepickerField'             => 'system/modules/con4gis_projects/classes/fieldtypes/C4GTimepickerField.php',
//    'c4g\projects\C4GTimestampField'             => 'system/modules/con4gis_projects/classes/fieldtypes/C4GTimestampField.php',
//    'c4g\projects\C4GUrlField'             => 'system/modules/con4gis_projects/classes/fieldtypes/C4GUrlField.php',
//    'c4g\projects\C4GDummyField'             => 'system/modules/con4gis_projects/classes/fieldtypes/C4GDummyField.php',
//    'c4g\projects\C4GTableField'             => 'system/modules/con4gis_projects/classes/fieldtypes/C4GTableField.php',
//
//    //Module Parent
//    'c4g\projects\C4GBrickModuleParent'     => 'system/modules/con4gis_projects/classes/framework/C4GBrickModuleParent.php',
//
//    //Models
//    '\c4g\projects\C4gProjectsLogbookModel'  => 'system/modules/con4gis_projects/models/C4gProjectsLogbookModel.php',
//    '\c4g\projects\C4gProjectsModel'         => 'system/modules/con4gis_projects/models/C4gProjectsModel.php',
//    '\c4g\projects\C4gProjectMapDataModel'   => 'system/modules/con4gis_projects/models/C4gProjectMapDataModel.php',
////
//    // Services
    'C4GBrickAjaxApi'       => 'system/modules/con4gis_projects/modules/api/C4GBrickAjaxApi.php',
//	'c4g\projects\C4GEditorTabApi'		=> 'system/modules/con4gis_projects/modules/api/C4GEditorTabApi.php',
//	'C4GStarboardTabApi'		=> 'system/modules/con4gis_projects/modules/api/C4GStarboardTabApi.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'mod_c4g_brick_list' => 'system/modules/con4gis_projects/templates/modules',
    'mod_c4g_brick_simple' => 'system/modules/con4gis_projects/templates/modules',
    'c4g_pdftemplate' => "system/modules/con4gis_projects/templates",
//    'be_c4g_brick_backup' => 'system/modules/con4gis_projects/templates/modules',
));
