<?php
/**
 * Created by PhpStorm.
 * User: cro
 * Date: 11.06.18
 * Time: 14:04
 */

namespace con4gis\ProjectsBundle\Entity;

use \Doctrine\ORM\Mapping as ORM;
use con4gis\CoreBundle\Entity\BaseEntity;

/**
 * Class TlC4GProjects
 *
 * @ORM\Entity
 * @ORM\Table(name="tl_c4g_projects")
 * @package con4gis\ProjectsBundle\Entity
 */
class TlC4GProjects extends BaseEntity
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id = 0;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    protected $tstamp = 0;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $uuid = '';

    /**
     * @var string
     * @ORM\Column(type="string", name="brick_key")
     */
    protected $brickKey = "";

    /**
     * @var int
     * @ORM\Column(type="integer", name="group_id")
     */
    protected $groupid = 0;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $caption = '';

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    protected $description = '';

    /**
     * @var int
     * @ORM\Column(name="last_member_id", type="integer")
     */
    protected $lastmemberid = 0;

    /**
     * @var int
     * @ORM\Column(name="is_frozen", type="integer", length=1)
     */
    protected $isfrozen = 0;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getTstamp()
    {
        return $this->tstamp;
    }

    /**
     * @param int $tstamp
     */
    public function setTstamp($tstamp)
    {
        $this->tstamp = $tstamp;
    }

    /**
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * @param string $uuid
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     * @return string
     */
    public function getBrickKey()
    {
        return $this->brickKey;
    }

    /**
     * @param string $brickKey
     */
    public function setBrickKey($brickKey)
    {
        $this->brickKey = $brickKey;
    }

    /**
     * @return int
     */
    public function getGroupid()
    {
        return $this->groupid;
    }

    /**
     * @param int $groupid
     */
    public function setGroupid($groupid)
    {
        $this->groupid = $groupid;
    }

    /**
     * @return string
     */
    public function getCaption()
    {
        return $this->caption;
    }

    /**
     * @param string $caption
     */
    public function setCaption($caption)
    {
        $this->caption = $caption;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return int
     */
    public function getLastmemberid()
    {
        return $this->lastmemberid;
    }

    /**
     * @param int $lastmemberid
     */
    public function setLastmemberid($lastmemberid)
    {
        $this->lastmemberid = $lastmemberid;
    }

    /**
     * @return int
     */
    public function getIsfrozen()
    {
        return $this->isfrozen;
    }

    /**
     * @param int $isfrozen
     */
    public function setIsfrozen($isfrozen)
    {
        $this->isfrozen = $isfrozen;
    }
}