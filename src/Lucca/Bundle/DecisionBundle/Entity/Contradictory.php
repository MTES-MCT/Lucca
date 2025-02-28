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

namespace Lucca\Bundle\MinuteBundle\Entity\DecisionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Lucca\CoreBundle\Entity\TimestampableTrait;
use Lucca\LogBundle\Entity\LogInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Contradictory
 *
 * @ORM\Table(name="lucca_minute_contradictory")
 * @ORM\Entity(repositoryClass="Lucca\MinuteBundle\Repository\ContradictoryRepository")
 *
 * @package Lucca\MinuteBundle\Entity
 * @author Terence <terence@numeric-wave.tech>
 */
class Contradictory implements LogInterface
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
     * @var \DateTime
     *
     * @ORM\Column(name="dateNoticeDdtm", type="datetime", nullable=true)
     * @Assert\DateTime(message = "constraint.datetime")
     */
    private $dateNoticeDdtm;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateExecution", type="datetime", nullable=true)
     * @Assert\DateTime(message = "constraint.datetime")
     */
    private $dateExecution;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateAnswer", type="datetime", nullable=true)
     * @Assert\DateTime(message = "constraint.datetime")
     */
    private $dateAnswer;

    /**
     * @var string
     *
     * @ORM\Column(name="answer", type="text", nullable=true)
     */
    private $answer;

    /************************************************************************ Custom functions ************************************************************************/

    /**
     * Log name of this Class
     * @return string
     */
    public function getLogName()
    {
        return 'Procédure contradictoire';
    }

    /********************************************************************* Automatic Getters & Setters *********************************************************************/

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set dateNoticeDdtm
     *
     * @param \DateTime $dateNoticeDdtm
     *
     * @return Contradictory
     */
    public function setDateNoticeDdtm($dateNoticeDdtm)
    {
        $this->dateNoticeDdtm = $dateNoticeDdtm;

        return $this;
    }

    /**
     * Get dateNoticeDdtm
     *
     * @return \DateTime
     */
    public function getDateNoticeDdtm()
    {
        return $this->dateNoticeDdtm;
    }

    /**
     * Set dateExecution
     *
     * @param \DateTime $dateExecution
     *
     * @return Contradictory
     */
    public function setDateExecution($dateExecution)
    {
        $this->dateExecution = $dateExecution;

        return $this;
    }

    /**
     * Get dateExecution
     *
     * @return \DateTime
     */
    public function getDateExecution()
    {
        return $this->dateExecution;
    }

    /**
     * Set dateAnswer
     *
     * @param \DateTime $dateAnswer
     *
     * @return Contradictory
     */
    public function setDateAnswer($dateAnswer)
    {
        $this->dateAnswer = $dateAnswer;

        return $this;
    }

    /**
     * Get dateAnswer
     *
     * @return \DateTime
     */
    public function getDateAnswer()
    {
        return $this->dateAnswer;
    }

    /**
     * Set answer
     *
     * @param string $answer
     *
     * @return Contradictory
     */
    public function setAnswer($answer)
    {
        $this->answer = $answer;

        return $this;
    }

    /**
     * Get answer
     *
     * @return string
     */
    public function getAnswer()
    {
        return $this->answer;
    }
}
