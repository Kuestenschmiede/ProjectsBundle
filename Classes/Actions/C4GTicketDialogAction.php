<?php
/**
 * Created by PhpStorm.
 * User: fsc
 * Date: 29.08.17
 * Time: 13:06
 */

namespace con4gis\ProjectsBundle\Classes\Actions;


use con4gis\ForumBundle\Resources\contao\modules\C4GForum;

class C4GTicketDialogAction extends C4GBrickDialogAction
{

    public function run()
    {
        $this->dialogParams->setRedirectSite($this->module->redirectsite);
        $this->dialogParams->setRedirectWithSaving(true);
        $redirect = new C4GRedirectAction($this->dialogParams,$this->listParams,$this->fieldList,$this->putVars,$this->brickDatabase);
        $action ='ticket';
        $action .=':'.$this->module->forum.':'.$this->dialogParams->getId().':'.$this->dialogParams->getGroupId();
        $redirect->setRedirectWithAction('state='.$action);
        if ($this->module->forum) {
            return $redirect->run();
        } else {
            //ToDo Language
            return array('title' => 'Fehlender Ticketbereich', 'usermessage' => 'Der Ticketbereich wurde nicht definiert. Bitte wenden Sie sich an den Betreiber der Website.');
        }

    }

}