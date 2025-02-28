<?php
/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

/*
 * copyright (c) 2025. numeric wave
 *
 * afero general public license (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */

namespace Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Lucca\CoreBundle\Entity\TimestampableTrait;
use Lucca\LogBundle\Entity\LogInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * CourierEdition
 *
 * @ORM\Table(name="lucca_minute_courier_edition")
 * @ORM\Entity(repositoryClass="Lucca\MinuteBundle\Repository\CourierEditionRepository")
 *
 * @package Lucca\MinuteBundle\Entity
 * @author Terence <terence@numeric-wave.tech>
 */
class CourierEdition implements LogInterface
{
    /** Traits */
    use TimestampableTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var bool
     *
     * @ORM\Column(name="judicialEdited", type="boolean")
     * @Assert\Type(type="bool", message="constraint.type")
     */
    private $judicialEdited = false;

    /**
     * @var string
     *
     * @ORM\Column(name="letterJudicial", type="text", nullable=true)
     */
    private $letterJudicial;

    /**
     * @var bool
     *
     * @ORM\Column(name="ddtmEdited", type="boolean")
     * @Assert\Type(type="bool", message="constraint.type")
     */
    private $ddtmEdited = false;

    /**
     * @var string
     *
     * @ORM\Column(name="letterDdtm", type="text", nullable=true)
     */
    private $letterDdtm;

    /************************************************************************ Custom functions ************************************************************************/

    /**
     * Log name of this Class
     * @return string
     */
    public function getLogName()
    {
        return 'Courrier édition';
    }

    /********************************************************************* Automatic Getters & Setters *********************************************************************/

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set judicialEdited.
     *
     * @param bool $judicialEdited
     *
     * @return CourierEdition
     */
    public function setJudicialEdited($judicialEdited)
    {
        $this->judicialEdited = $judicialEdited;

        return $this;
    }

    /**
     * Get judicialEdited.
     *
     * @return bool
     */
    public function getJudicialEdited()
    {
        return $this->judicialEdited;
    }

    /**
     * Set letterJudicial.
     *
     * @param string|null $letterJudicial
     *
     * @return CourierEdition
     */
    public function setLetterJudicial($letterJudicial = null)
    {
        $this->letterJudicial = $letterJudicial;

        return $this;
    }

    /**
     * Get letterJudicial.
     *
     * @return string|null
     */
    public function getLetterJudicial()
    {
        return $this->letterJudicial;
    }

    /**
     * Set ddtmEdited.
     *
     * @param bool $ddtmEdited
     *
     * @return CourierEdition
     */
    public function setDdtmEdited($ddtmEdited)
    {
        $this->ddtmEdited = $ddtmEdited;

        return $this;
    }

    /**
     * Get ddtmEdited.
     *
     * @return bool
     */
    public function getDdtmEdited()
    {
        return $this->ddtmEdited;
    }

    /**
     * Set letterDdtm.
     *
     * @param string|null $letterDdtm
     *
     * @return CourierEdition
     */
    public function setLetterDdtm($letterDdtm = null)
    {
        $this->letterDdtm = $letterDdtm;

        return $this;
    }

    /**
     * Get letterDdtm.
     *
     * @return string|null
     */
    public function getLetterDdtm()
    {
        return $this->letterDdtm;
    }
}
