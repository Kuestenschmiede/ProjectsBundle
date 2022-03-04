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
namespace con4gis\ProjectsBundle\Classes\Actions;

class C4GTicketDialogAction extends C4GBrickDialogAction
{
    public function run()
    {
        $redirect = new C4GRedirectAction($this->dialogParams, $this->listParams, $this->fieldList, $this->putVars, $this->brickDatabase);
        $redirect->setRedirectSite($this->module->ticketcenter);
        $this->dialogParams->setRedirectWithSaving(true);
        $action = 'ticketcall';
        $ticketSubject = $this->module->getBrickCaption() . ' ' . $this->dialogParams->getId() . ' (';
        if ($this->module->getCaptionField()) {
            $captionFields = explode(':', $this->module->getCaptionField());
            foreach ($captionFields as $captionField) {
                if (substr($captionField, 0, 1) == '{') {
                    $parentFields = explode(';', substr($captionField, 1, strlen($captionField) - 2));
                    if (!$parentFields[1]) {
                        $ticketSubject .= substr($captionField, 1, strlen($captionField) - 2);

                        continue;
                    }
                    $pid = $this->putVars[$parentFields[1]];
                    if (!$pid) {
                        $pid = $this->dialogParams->getParentId();
                    }
                    $ticketSubject .= ' ';
                    $parentModule = $this->brickDatabase->getParams()->getDatabase()->prepare('SELECT * FROM ' . $parentFields[0] . ' WHERE id=?')->execute($pid)->fetchAssoc();
                    for ($i = 2; $i < count($parentFields);$i++) {
                        if ($parentModule[$parentFields[$i]]) {
                            $ticketSubject .= $parentModule[$parentFields[$i]] . ' ';
                        }
                    }
                }
                if ($this->putVars[$captionField]) {
                    $ticketSubject .= $this->putVars[$captionField] . ' ';
                }
            }
        } elseif ($this->putVars[$this->module->getCaptionField()]) {
            $ticketSubject .= $this->putVars[$this->module->getCaptionField()];
        }

        $ticketSubject .= ')';
        $action .= ':' . $this->module->forum . ':' . $this->dialogParams->getId() . ':' . $this->dialogParams->getGroupId() . ':' . $ticketSubject;
        $redirect->setRedirectWithAction('state=' . $action);
        if ($this->module->forum) {
            return $redirect->run();
        }

        return ['title' => $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_MISSING_TICKET_TITLE'], 'usermessage' => $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_MISSING_TICKET']];
    }

    public function isReadOnly()
    {
        return true;
    }
}
