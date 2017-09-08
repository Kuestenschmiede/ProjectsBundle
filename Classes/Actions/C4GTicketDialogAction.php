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
        $redirect = new C4GRedirectAction($this->dialogParams,$this->listParams,$this->fieldList,$this->putVars,$this->brickDatabase);
        $redirect->setRedirectSite($this->module->ticketcenter);
        $this->dialogParams->setRedirectWithSaving(true);
        $action ='ticketcall';
        $ticketSubject = $this->module->getBrickCaption().' '.$this->dialogParams->getId().' (';
        if ($this->module->getCaptionField()){
            $captionFields = explode(':',$this->module->getCaptionField());
            foreach ($captionFields as $captionField)
            {
                if($this->putVars[$captionField]){
                    $ticketSubject .= $this->putVars[$captionField].' ';
                }
            }
        } else if ($this->putVars[$this->module->getCaptionField()]){
            $ticketSubject .= $this->putVars[$this->module->getCaptionField()];
        }


        $ticketSubject .= ')';
        $action .=':'.$this->module->forum.':'.$this->dialogParams->getId().':'.$this->dialogParams->getGroupId().':'.$ticketSubject;
        $redirect->setRedirectWithAction('state='.$action);
        if ($this->module->forum) {
            return $redirect->run();
        } else {
            //ToDo Language
            return array('title' => 'Fehlender Ticketbereich', 'usermessage' => 'Der Ticketbereich wurde nicht definiert. Bitte wenden Sie sich an den Betreiber der Website.');
        }

    }

}