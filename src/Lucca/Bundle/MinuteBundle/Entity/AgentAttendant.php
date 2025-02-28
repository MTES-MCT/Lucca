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

namespace Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Lucca\CoreBundle\Entity\TimestampableTrait;
use Lucca\LogBundle\Entity\LogInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Agent
 *
 * @ORM\Table(name="lucca_minute_agent_attendant")
 * @ORM\Entity(repositoryClass="Lucca\MinuteBundle\Repository\AgentRepository")
 *
 * @package Lucca\MinuteBundle\Entity
 * @author Terence <terence@numeric-wave.tech>
 */
class AgentAttendant implements LogInterface
{
    /** Traits */
    use TimestampableTrait;

    /** FUNCTION constants */
    const FUNCTION_MAYOR = 'choice.function.mayor';
    const FUNCTION_DEPUTY = 'choice.function.deputy';
    const FUNCTION_POLICE = 'choice.function.police';
    const FUNCTION_DGS = 'choice.function.dgs';
    const FUNCTION_DST = 'choice.function.dst';
    const FUNCTION_TOWN_MANAGER = 'choice.function.town_manager';
    const FUNCTION_ADMIN_AGENT = 'choice.function.admin_agent';
    const FUNCTION_COUNTRY_AGENT = 'choice.function.country_agent';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50)
     * @Assert\NotNull(message="constraint.not_null")
     * @Assert\Type(type="string", message="constraint.type")
     * @Assert\Length(
     *      min = 2, max = 50,
     *      minMessage = "constraint.length.min",
     *      maxMessage = "constraint.length.max",
     * )
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=50)
     * @Assert\NotNull(message="constraint.not_null")
     * @Assert\Type(type="string", message="constraint.type")
     * @Assert\Length(
     *      min = 2, max = 50,
     *      minMessage = "constraint.length.min",
     *      maxMessage = "constraint.length.max",
     * )
     */
    private $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="function", type="string", length=50)
     * @Assert\NotNull(message="constraint.not_null")
     * @Assert\Type(type="string", message="constraint.type")
     * @Assert\Length(
     *      min = 2, max = 50,
     *      minMessage = "constraint.length.min",
     *      maxMessage = "constraint.length.max",
     * )
     */
    private $function;

    /************************************************************************ Custom functions ************************************************************************/

    /**
     * Get full name with official syntax
     *
     * @return string
     */
    public function getOfficialName()
    {
        return $this->getName() . ' ' . $this->getFirstname();
    }

    /**
     * Get label display on form
     *
     * @return string
     */
    public function getFormLabel()
    {
        return '(' . $this->getId() . ') ' . $this->getName() . ' ' . $this->getFirstname();
    }

    /**
     * Log name of this Class
     * @return string
     */
    public function getLogName()
    {
        return 'Agent accompagnant';
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
     * Set name
     *
     * @param string $name
     *
     * @return AgentAttendant
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     *
     * @return AgentAttendant
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set function
     *
     * @param string $function
     *
     * @return AgentAttendant
     */
    public function setFunction($function)
    {
        $this->function = $function;

        return $this;
    }

    /**
     * Get function
     *
     * @return string
     */
    public function getFunction()
    {
        return $this->function;
    }
}
