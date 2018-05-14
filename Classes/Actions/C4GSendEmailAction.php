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

namespace con4gis\ProjectsBundle\Classes\Actions;

use con4gis\CoreBundle\Resources\contao\classes\C4GUtils;

class C4GSendEmailAction extends C4GBrickDialogAction
{
    private $recipient = '';
    private $senderName = '';
    private $text = '';

    public function run()
    {
        $mailData = array();

        // reciever
        $mailData['to'] = trim($this->recipient);

        $mailData['charset'] = 'utf-8';

        // subject
        $mailData['subject'] = sprintf( $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['EMAIL_A_MESSAGE_FROM'] , $this->senderName );

        // message-text
        $mailData['text'] = str_replace(array('[MEMBER]', '[MESSAGE]'), array($this->senderName, $this->text), $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['EMAIL_MESSAGE']);

        // send mail
        return C4GUtils::sendMail( $mailData );

    }

    /**
     * @return string
     */
    public function getRecipient()
    {
        return $this->recipient;
    }

    /**
     * @param $recipient
     * @return $this
     */
    public function setRecipient($recipient)
    {
        $this->recipient = $recipient;
        return $this;
    }

    /**
     * @return string
     */
    public function getSenderName()
    {
        return $this->senderName;
    }

    /**
     * @param $senderName
     * @return $this
     */
    public function setSenderName($senderName)
    {
        $this->senderName = $senderName;
        return $this;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param $text
     * @return $this
     */
    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    public function isReadOnly()
    {
        return false;
    }


}
