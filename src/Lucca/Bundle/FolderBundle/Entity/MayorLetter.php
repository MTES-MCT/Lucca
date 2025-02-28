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
 * Plot
 *
 * @ORM\Table(name="lucca_minute_mayor_letter")
 * @ORM\Entity(repositoryClass="Lucca\MinuteBundle\Repository\MayorLetterRepository")
 *
 * @package Lucca\MinuteBundle\Entity
 * @author Lisa <lisa.alvarez@numeric-wave.eu>
 */
class MayorLetter implements LogInterface
{
    /** Traits */
    use TimestampableTrait;

    /** GENDER constants */
    const GENDER_MALE = 'choice.gender.male';
    const GENDER_FEMALE = 'choice.gender.female';

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
     * @ORM\Column(name="gender", type="string", length=30)
     * @Assert\NotNull(message="constraint.not_null")
     * @Assert\Type(type="string", message="constraint.type")
     * @Assert\Length(
     *      min = 2, max = 30,
     *      minMessage = "constraint.length.min",
     *      maxMessage = "constraint.length.max",
     * )
     */
    private $gender;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=150)
     * @Assert\NotNull(message="constraint.not_null")
     * @Assert\Type(type="string", message="constraint.type")
     * @Assert\Length(
     *      min = 2, max = 150,
     *      minMessage = "constraint.length.min",
     *      maxMessage = "constraint.length.max",
     * )
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255, nullable=false)
     * @Assert\Type(type="string", message="constraint.type")
     * @Assert\Length(
     *      min = 2, max = 255,
     *      minMessage = "constraint.length.min",
     *      maxMessage = "constraint.length.max",
     * )
     */
    private $address;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateSended", type="datetime", nullable=true)
     * @Assert\DateTime(message = "constraint.datetime")
     */
    private $dateSended;

    /**
     * @ORM\ManyToOne(targetEntity="Lucca\ParameterBundle\Entity\Town")
     * @ORM\JoinColumn(nullable=false)
     */
    private $town;

    /**
     * @ORM\ManyToOne(targetEntity="Lucca\AdherentBundle\Entity\Adherent", inversedBy="agents")
     * @ORM\JoinColumn(nullable=false)
     */
    private $adherent;

    /**
     * @ORM\ManyToOne(targetEntity="Lucca\AdherentBundle\Entity\Agent")
     * @ORM\JoinColumn(nullable=true)
     */
    private $agent;

    /**
     * Many MayorLetter have Many Folder
     * Delete Folder - Delete on join table specific rows
     *
     * @ORM\ManyToMany(targetEntity="Lucca\MinuteBundle\Entity\Folder", cascade={"persist"})
     * @ORM\JoinTable(name="lucca_mayor_letter_linked_folder",
     *      joinColumns={@ORM\JoinColumn(name="mayor_letter_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="folder_id", referencedColumnName="id", onDelete="CASCADE")}
     *      )
     */
    private $folders;

    /************************************************************************ Custom functions ************************************************************************/

    /**
     * Log name of this Class
     * Same as "Lettre au maire"
     * @return string
     */
    public function getLogName()
    {
        return 'Courrier de rattachement';
    }

    /**
     * ! Set folders overwrite all folders previously add
     *
     * @param array $folders
     */
    public function setFolders(array $folders) {
        $this->folders = new \Doctrine\Common\Collections\ArrayCollection($folders);
    }

    /********************************************************************* Automatic Getters & Setters *********************************************************************/

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->folders = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * Set gender.
     *
     * @param string $gender
     *
     * @return MayorLetter
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender.
     *
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return MayorLetter
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set address.
     *
     * @param string $address
     *
     * @return MayorLetter
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address.
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set dateSended.
     *
     * @param \DateTime $dateSended
     *
     * @return MayorLetter
     */
    public function setDateSended($dateSended)
    {
        $this->dateSended = $dateSended;

        return $this;
    }

    /**
     * Get dateSended.
     *
     * @return \DateTime
     */
    public function getDateSended()
    {
        return $this->dateSended;
    }

    /**
     * Set town.
     *
     * @param \Lucca\ParameterBundle\Entity\Town $town
     *
     * @return MayorLetter
     */
    public function setTown(\Lucca\ParameterBundle\Entity\Town $town)
    {
        $this->town = $town;

        return $this;
    }

    /**
     * Get town.
     *
     * @return \Lucca\ParameterBundle\Entity\Town
     */
    public function getTown()
    {
        return $this->town;
    }

    /**
     * Set adherent.
     *
     * @param \Lucca\AdherentBundle\Entity\Adherent $adherent
     *
     * @return MayorLetter
     */
    public function setAdherent(\Lucca\AdherentBundle\Entity\Adherent $adherent)
    {
        $this->adherent = $adherent;

        return $this;
    }

    /**
     * Get adherent.
     *
     * @return \Lucca\AdherentBundle\Entity\Adherent
     */
    public function getAdherent()
    {
        return $this->adherent;
    }

    /**
     * Set agent.
     *
     * @param \Lucca\AdherentBundle\Entity\Agent|null $agent
     *
     * @return MayorLetter
     */
    public function setAgent(\Lucca\AdherentBundle\Entity\Agent $agent = null)
    {
        $this->agent = $agent;

        return $this;
    }

    /**
     * Get agent.
     *
     * @return \Lucca\AdherentBundle\Entity\Agent|null
     */
    public function getAgent()
    {
        return $this->agent;
    }

    /**
     * Add folder.
     *
     * @param \Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\Folder $folder
     *
     * @return MayorLetter
     */
    public function addFolder(\Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\Folder $folder)
    {
        $this->folders[] = $folder;

        return $this;
    }

    /**
     * Remove folder.
     *
     * @param \Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\Folder $folder
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeFolder(\Lucca\Bundle\MinuteBundle\Entity\FolderBundle\Entity\Folder $folder)
    {
        return $this->folders->removeElement($folder);
    }

    /**
     * Get folders.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFolders()
    {
        return $this->folders;
    }
}
