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
        return \c4g\C4GUtils::sendMail( $mailData );

    }

    /**
     * @return string
     */
    public function getRecipient()
    {
        return $this->recipient;
    }

    /**
     * @param string $recipient
     */
    public function setRecipient($recipient)
    {
        $this->recipient = $recipient;
    }

    /**
     * @return string
     */
    public function getSenderName()
    {
        return $this->senderName;
    }

    /**
     * @param string $senderName
     */
    public function setSenderName($senderName)
    {
        $this->senderName = $senderName;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }


}
