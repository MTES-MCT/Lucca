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
 * Tag
 *
 * @ORM\Table(name="lucca_tag")
 * @ORM\Entity(repositoryClass="Lucca\MinuteBundle\Repository\TagRepository")
 *
 * @package Lucca\MinuteBundle\Entity
 * @author Terence <terence@numeric-wave.tech>
 */
class Tag implements LogInterface
{
    use ToggleableTrait, TimestampableTrait;

    /** TYPE constants */
    const CATEGORY_NATURE = 'choice.category.nature';
    const CATEGORY_TOWN = 'choice.category.town';

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
     * @ORM\Column(name="num", type="smallint")
     * @Assert\NotNull(message="constraint.not_null")
     * @Assert\Type(type="int", message="constraint.type")
     */
    private $num;

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
     * @ORM\Column(name="category", type="string", length=30, nullable=true)
     * @Assert\Type(type="string", message="constraint.type")
     * @Assert\Length(
     *      min = 2, max = 30,
     *      minMessage = "constraint.length.min",
     *      maxMessage = "constraint.length.max",
     * )
     */
    private $category;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\OneToMany(
     *     targetEntity="Lucca\MinuteBundle\Entity\Proposal", mappedBy="tag",
     *     cascade={"persist", "remove"}, orphanRemoval=true
     * )
     */
    private $proposals;

    /************************************************************************ Custom functions ************************************************************************/

    /**
     * Tag constructor
     */
    public function __construct()
    {
        $this->proposals = new ArrayCollection();
    }

    /**
     * Add proposal
     * Override function
     *
     * @param \Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\Proposal $proposal
     * @return Tag
     */
    public function addProposal(\Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\Proposal $proposal)
    {
        $this->proposals[] = $proposal;
        $proposal->setTag($this);

        return $this;
    }

    /**
     * Log name of this Class
     * @return string
     */
    public function getLogName()
    {
        return 'Mot clé';
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
     * @return Tag
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
     * Set name
     *
     * @param string $name
     *
     * @return Tag
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
     * Set category
     *
     * @param string $category
     *
     * @return Tag
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Tag
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
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
     * Remove proposal
     *
     * @param \Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\Proposal $proposal
     */
    public function removeProposal(\Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\Proposal $proposal)
    {
        $this->proposals->removeElement($proposal);
    }

    /**
     * Get proposals
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProposals()
    {
        return $this->proposals;
    }
}
