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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Lucca\CoreBundle\Entity\TimestampableTrait;
use Lucca\CoreBundle\Entity\ToggleableTrait;
use Lucca\LogBundle\Entity\LogInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Natinf
 *
 * @ORM\Table(name="lucca_natinf")
 * @ORM\Entity(repositoryClass="Lucca\MinuteBundle\Repository\NatinfRepository")
 *
 * @package Lucca\MinuteBundle\Entity
 * @author Terence <terence@numeric-wave.tech>
 */
class Natinf implements LogInterface
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
     * @var int
     *
     * @ORM\Column(name="num", type="integer")
     * @Assert\NotNull(message="constraint.not_null")
     * @Assert\Type(type="int", message="constraint.type")
     */
    private $num;

    /**
     * @var string
     *
     * @ORM\Column(name="qualification", type="string", length=255)
     * @Assert\NotNull(message="constraint.not_null")
     */
    private $qualification;

    /**
     * @var string
     *
     * @ORM\Column(name="definedBy", type="string", length=255)
     * @Assert\NotNull(message="constraint.not_null")
     */
    private $definedBy;

    /**
     * @var string
     *
     * @ORM\Column(name="repressedBy", type="string", length=255)
     * @Assert\NotNull(message="constraint.not_null")
     */
    private $repressedBy;

    /**
     * @ORM\ManyToMany(targetEntity="Lucca\MinuteBundle\Entity\Tag")
     * @ORM\JoinTable(name="lucca_natinf_linked_tag",
     *      joinColumns={@ORM\JoinColumn(name="natinf_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="tag_id", referencedColumnName="id")}
     * )
     */
    private $tags;

    /**
     * @ORM\ManyToOne(targetEntity="Lucca\MinuteBundle\Entity\Natinf")
     * @ORM\JoinColumn(nullable=true)
     */
    private $parent;

    /************************************************************************ Custom functions ************************************************************************/

    /**
     * Natinf constructor
     */
    public function __construct()
    {
        $this->tags = new ArrayCollection();
    }

    /**
     * Label use on form
     *
     * @return string
     */
    public function getFormLabel()
    {
        return $this->getNum() . ' / ' . $this->getQualification();
    }

    /**
     * Check if tag exist in array
     * @param Tag $tag
     * @return bool
     */
    public function hasTag(\Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\Tag $tag)
    {
        if ($this->getTags()) {
            foreach ($this->getTags() as $element) {
                if ($element === $tag)
                    return true;
            }
        }

        return false;
    }

    /**
     * Log name of this Class
     * @return string
     */
    public function getLogName()
    {
        return 'Natinf';
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
     * Set num
     *
     * @param integer $num
     *
     * @return Natinf
     */
    public function setNum($num)
    {
        $this->num = $num;

        return $this;
    }

    /**
     * Get num
     *
     * @return integer
     */
    public function getNum()
    {
        return $this->num;
    }

    /**
     * Set qualification
     *
     * @param string $qualification
     *
     * @return Natinf
     */
    public function setQualification($qualification)
    {
        $this->qualification = $qualification;

        return $this;
    }

    /**
     * Get qualification
     *
     * @return string
     */
    public function getQualification()
    {
        return $this->qualification;
    }

    /**
     * Set definedBy
     *
     * @param string $definedBy
     *
     * @return Natinf
     */
    public function setDefinedBy($definedBy)
    {
        $this->definedBy = $definedBy;

        return $this;
    }

    /**
     * Get definedBy
     *
     * @return string
     */
    public function getDefinedBy()
    {
        return $this->definedBy;
    }

    /**
     * Set repressedBy
     *
     * @param string $repressedBy
     *
     * @return Natinf
     */
    public function setRepressedBy($repressedBy)
    {
        $this->repressedBy = $repressedBy;

        return $this;
    }

    /**
     * Get repressedBy
     *
     * @return string
     */
    public function getRepressedBy()
    {
        return $this->repressedBy;
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
     * Add tag
     *
     * @param \Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\Tag $tag
     *
     * @return Natinf
     */
    public function addTag(\Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\Tag $tag)
    {
        $this->tags[] = $tag;

        return $this;
    }

    /**
     * Remove tag
     *
     * @param \Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\Tag $tag
     */
    public function removeTag(\Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\Tag $tag)
    {
        $this->tags->removeElement($tag);
    }

    /**
     * Get tags
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Set parent
     *
     * @param \Lucca\MinuteBundle\Entity\Natinf $parent
     *
     * @return Natinf
     */
    public function setParent(\Lucca\MinuteBundle\Entity\Natinf $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \Lucca\MinuteBundle\Entity\Natinf
     */
    public function getParent()
    {
        return $this->parent;
    }
}
