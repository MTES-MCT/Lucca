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
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Plot
 *
 * @ORM\Table(name="lucca_minute_plot")
 * @ORM\Entity(repositoryClass="Lucca\MinuteBundle\Repository\PlotRepository")
 *
 * @package Lucca\MinuteBundle\Entity
 * @author Terence <terence@numeric-wave.tech>
 */
class Plot implements LogInterface
{
    /** Traits */
    use TimestampableTrait;

    /** RISK constants */
    const RISK_FLOOD = 'choice.risk.flood';
    const RISK_FIRE = 'choice.risk.fire';
    const RISK_AVALANCHE = 'choice.risk.avalanche';
    const RISK_GROUND_MOVEMENT = 'choice.risk.groundMovement';
    const RISK_TECHNOLOGICAL = 'choice.risk.technological';
    const RISK_OTHER = 'choice.risk.other';

    /** LocationFrom constants */
    const LOCATION_FROM_ADDRESS = 'choice.locationFrom.address';
    const LOCATION_FROM_COORDINATES = 'choice.locationFrom.coordinates';
    const LOCATION_FROM_MANUAL = 'choice.locationFrom.manual';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Lucca\ParameterBundle\Entity\Town")
     * @ORM\JoinColumn(nullable=false)
     */
    private $town;

    /**
     * @var string
     *
     * @ORM\Column(name="parcel", type="string", length=50, nullable=true)
     * @Assert\Type(type="string", message="constraint.type")
     * @Assert\Length(
     *      min = 2, max = 50,
     *      minMessage = "constraint.length.min",
     *      maxMessage = "constraint.length.max",
     * )
     */
    private $parcel;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255, nullable=true)
     * @Assert\Type(type="string", message="constraint.type")
     * @Assert\Length(
     *      min = 2, max = 255,
     *      minMessage = "constraint.length.min",
     *      maxMessage = "constraint.length.max",
     * )
     */
    private $address;

    /**
     * @var bool
     *
     * @ORM\Column(name="isRiskZone", type="boolean", nullable=true)
     * @Assert\Type(type="bool", message="constraint.type")
     */
    private $isRiskZone;

    /**
     * @var string
     *
     * @ORM\Column(name="risk", type="string", length=50, nullable=true)
     * @Assert\Type(type="string", message="constraint.type")
     * @Assert\Length(
     *      min = 2, max = 50,
     *      minMessage = "constraint.length.min",
     *      maxMessage = "constraint.length.max",
     * )
     */
    private $risk;

    /**
     * @var string
     *
     * @ORM\Column(name="place", type="string", length=50, nullable=true)
     * @Assert\Type(type="string", message="constraint.type")
     * @Assert\Length(
     *      min = 2, max = 50,
     *      minMessage = "constraint.length.min",
     *      maxMessage = "constraint.length.max",
     * )
     */
    private $place;

    /**
     * @var string
     *
     * @ORM\Column(name="latitude", type="decimal", precision=40, scale=30, nullable=true)
     */
    private $latitude;

    /**
     * @var string
     *
     * @ORM\Column(name="longitude", type="decimal", precision=40, scale=30, nullable=true)
     */
    private $longitude;

    /**
     * @var string
     *
     * @ORM\Column(name="locationFrom", type="string", length=50, nullable=false)
     * @Assert\Type(type="string", message="constraint.type")
     * @Assert\Choice(
     *      choices = {
     *          Plot::LOCATION_FROM_ADDRESS,
     *          Plot::LOCATION_FROM_COORDINATES,
     *          Plot::LOCATION_FROM_MANUAL
     *      }, message = "constraint.choice.status"
     * )
     */
    private $locationFrom;

    /************************************************************************ Custom functions ************************************************************************/

    /**
     * Constraint on Plot
     * Plot Address or Plot Place must be filled
     *
     * @Assert\Callback()
     * @param ExecutionContextInterface $context
     */
    public function plotConstraint(ExecutionContextInterface $context)
    {
        if (!$this->getAddress() && !$this->getPlace() && !$this->getLongitude() && !$this->getLatitude())
            $context->buildViolation('constraint.plot.address_or_parcel')
                ->atPath('address')
                ->addViolation();
        if (!$this->getAddress() && !$this->getPlace() && !$this->getLongitude() && !$this->getLatitude())
            $context->buildViolation('constraint.plot.address_or_parcel')
                ->atPath('place')
                ->addViolation();

        if (!$this->getAddress() && !$this->getPlace() && !$this->getLongitude() && !$this->getLatitude())
            $context->buildViolation('constraint.plot.locationNeeded')
                ->atPath('longitude')
                ->addViolation();

        if (!$this->getAddress() && !$this->getPlace() && !$this->getLongitude() && !$this->getLatitude())
            $context->buildViolation('constraint.plot.locationNeeded')
                ->atPath('latitude')
                ->addViolation();
    }

    /**
     * Log name of this Class
     * @return string
     */
    public function getLogName()
    {
        return 'Parcelle';
    }

    /**
     * Get full address
     * @return string
     */
    public function getFullAddress()
    {
        $address = '';
        if ($this->getAddress())
            $address .= $this->getAddress() . ' ';
        if ($this->getPlace())
            $address .= $this->getPlace() . ' ';
        if ($this->getTown())
        $address .= $this->getTown()->getName() . ' - ' . $this->getTown()->getCode() . ' ';

        return $address;
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
     * Set parcel.
     *
     * @param string|null $parcel
     *
     * @return Plot
     */
    public function setParcel($parcel = null)
    {
        $this->parcel = $parcel;

        return $this;
    }

    /**
     * Get parcel.
     *
     * @return string|null
     */
    public function getParcel()
    {
        return $this->parcel;
    }

    /**
     * Set address.
     *
     * @param string|null $address
     *
     * @return Plot
     */
    public function setAddress($address = null)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address.
     *
     * @return string|null
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set isRiskZone.
     *
     * @param bool|null $isRiskZone
     *
     * @return Plot
     */
    public function setIsRiskZone($isRiskZone = null)
    {
        $this->isRiskZone = $isRiskZone;

        return $this;
    }

    /**
     * Get isRiskZone.
     *
     * @return bool|null
     */
    public function getIsRiskZone()
    {
        return $this->isRiskZone;
    }

    /**
     * Set risk.
     *
     * @param string|null $risk
     *
     * @return Plot
     */
    public function setRisk($risk = null)
    {
        $this->risk = $risk;

        return $this;
    }

    /**
     * Get risk.
     *
     * @return string|null
     */
    public function getRisk()
    {
        return $this->risk;
    }

    /**
     * Set place.
     *
     * @param string|null $place
     *
     * @return Plot
     */
    public function setPlace($place = null)
    {
        $this->place = $place;

        return $this;
    }

    /**
     * Get place.
     *
     * @return string|null
     */
    public function getPlace()
    {
        return $this->place;
    }

    /**
     * Set latitude.
     *
     * @param string|null $latitude
     *
     * @return Plot
     */
    public function setLatitude($latitude = null)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get latitude.
     *
     * @return string|null
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set longitude.
     *
     * @param string|null $longitude
     *
     * @return Plot
     */
    public function setLongitude($longitude = null)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get longitude.
     *
     * @return string|null
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set locationFrom.
     *
     * @param string $locationFrom
     *
     * @return Plot
     */
    public function setLocationFrom($locationFrom)
    {
        $this->locationFrom = $locationFrom;

        return $this;
    }

    /**
     * Get locationFrom.
     *
     * @return string
     */
    public function getLocationFrom()
    {
        return $this->locationFrom;
    }

    /**
     * Set town.
     *
     * @param \Lucca\ParameterBundle\Entity\Town $town
     *
     * @return Plot
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
}
