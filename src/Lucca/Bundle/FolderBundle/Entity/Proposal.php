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
use Lucca\CoreBundle\Entity\ToggleableTrait;
use Lucca\LogBundle\Entity\LogInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Proposal
 *
 * @ORM\Table(name="lucca_proposal")
 * @ORM\Entity(repositoryClass="Lucca\MinuteBundle\Repository\ProposalRepository")
 *
 * @package Lucca\MinuteBundle\Entity
 * @author Terence <terence@numeric-wave.tech>
 */
class Proposal implements LogInterface
{
    use ToggleableTrait, TimestampableTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Lucca\MinuteBundle\Entity\Tag", inversedBy="proposals")
     * @ORM\JoinColumn(nullable=false)
     */
    private $tag;

    /**
     * @var string
     *
     * @ORM\Column(name="sentence", type="text")
     * @Assert\NotNull(message="constraint.not_null")
     */
    private $sentence;

    /************************************************************************ Custom functions ************************************************************************/

    /**
     * Log name of this Class
     * @return string
     */
    public function getLogName()
    {
        return 'Proposition';
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
     * Set sentence
     *
     * @param string $sentence
     *
     * @return Proposal
     */
    public function setSentence($sentence)
    {
        $this->sentence = $sentence;

        return $this;
    }

    /**
     * Get sentence
     *
     * @return string
     */
    public function getSentence()
    {
        return $this->sentence;
    }

    /**
     * Get enabled
     *
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set tag
     *
     * @param \Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\Tag $tag
     *
     * @return Proposal
     */
    public function setTag(\Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\Tag $tag)
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * Get tag
     *
     * @return \Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\Tag
     */
    public function getTag()
    {
        return $this->tag;
    }
}
