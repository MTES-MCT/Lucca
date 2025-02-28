<?php
/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\MinuteBundle\Utils;

use Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Plot;
use Doctrine\ORM\EntityManager;
use Lucca\CoreBundle\Utils\GeoLocator;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;

/**
 * Class PlotManager
 *
 * @package Lucca\MinuteBundle\Utils
 * @author Alizee Meyer <alizee.m@numeric-wave.eu>
 */
class PlotManager
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var GeoLocator
     */
    private $geoLocator;

    /**
     * @var FlashBag
     */
    private $flashBag;

    /**
     * PlotManager constructor.
     *
     * @param EntityManager $entityManager
     * @param GeoLocator $p_geoLocator
     * @param FlashBag $p_flashBag
     */
    public function __construct(EntityManager $entityManager, GeoLocator $p_geoLocator, FlashBag $p_flashBag)
    {
        $this->em = $entityManager;
        $this->geoLocator = $p_geoLocator;
        $this->flashBag = $p_flashBag;
    }

    /** Manage location of plot depends on the value of location from
     *
     * @param Plot $p_plot
     */
    public function manageLocation(Plot $p_plot)
    {
        if ($p_plot->getLocationFrom() === PLOT::LOCATION_FROM_ADDRESS) {

            /** Use geolocator service to find geocode */
            $this->geoLocator->addGeocodeFromAddress($p_plot);

            if ($p_plot->getLatitude() === NULL || $p_plot->getLongitude() === NULL) {
                $this->flashBag->add('warning', 'flash.plot.geolocalisationFailed');
            }
        } elseif ($p_plot->getLocationFrom() === PLOT::LOCATION_FROM_COORDINATES) {
            /** Use geolocator service to find address */
            $address = $this->geoLocator->getAddressFromGeocode($p_plot->getLatitude(), $p_plot->getLongitude());

            $addrRoute = '';

            /** add route number */
            if (array_key_exists('addrNumber', $address))
                $addrRoute .= $address['addrNumber'] . ' ';

            /** add address name and set in plat */
            if (array_key_exists('addrRoute', $address)) {
                $addrRoute .= $address['addrRoute'];
                $p_plot->setAddress($addrRoute);
            }

            $town = $this->em->getRepository('LuccaParameterBundle:Town')->findOneBy(array(
                'zipcode' => $address['addrCode'],
                'name' => $address['addrCity']
            ));

            /** If there is no town display error message because it can cause issues */
            if ($town === NULL) {
                $this->flashBag->add('warning', 'flash.plot.geolocalisationFailedCoordinates');
            } else {
                $p_plot->setTown($town);
            }
        } else {
            /** Use geolocator service to find city */
            $address = $this->geoLocator->getAddressFromGeocode($p_plot->getLatitude(), $p_plot->getLongitude());
            if ($address['addrCode'])
                $town = $this->em->getRepository('LuccaParameterBundle:Town')->findOneBy(array(
                    'zipcode' => $address['addrCode'],
                    'name' => $address['addrCity']
                ));
            else
                $town = $this->em->getRepository('LuccaParameterBundle:Town')->findOneBy(array(
                    'name' => $address['addrCity']
                ));

            /** If there is no town display error message because it can cause issues */
            if ($town === NULL) {
                $this->flashBag->add('warning', 'flash.plot.geolocalisationFailedManual');
            } else {
                $p_plot->setTown($town);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'lucca.manager.plot';
    }
}
