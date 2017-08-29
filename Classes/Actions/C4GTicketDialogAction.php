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
        $c4gForum = C4GForum::class;
        $c4gForum->performAction('newthread');
    }

}