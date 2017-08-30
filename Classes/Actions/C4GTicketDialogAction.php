<?php
/**
 * Created by PhpStorm.
 * User: fsc
 * Date: 29.08.17
 * Time: 13:06
 */

namespace con4gis\ProjectBundle\Classes\Actions;


use con4gis\ForumBundle\Resources\contao\modules\C4GForum;

class C4GTicketDialogAction extends C4GBrickDialogAction
{

    public function run()
    {

        $Forum = new C4GForum(\ModuleModel::findByPk($this->module->forum));
        $this->dialogParams->setRedirectSite($this->module->redirectsite);
        $this->dialogParams->setRedirectWithSaving(true);
        $redirect = new C4GRedirectAction($this->dialogParams,$this->listParams,$this->fieldList,$this->putVars,$this->brickDatabase);
        $action ='ticket';
        $action .=':'.$this->module->forum.':'.$this->dialogParams->getId().':'.$this->dialogParams->getGroupId();
        $redirect->setRedirectWithAction('state='.$action);
        return $redirect->run();

    }

}