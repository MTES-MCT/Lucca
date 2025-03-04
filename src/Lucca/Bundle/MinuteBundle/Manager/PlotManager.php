<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MinuteBundle\Manager;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\RequestStack;

use Lucca\Bundle\CoreBundle\Service\GeoLocator;
use Lucca\Bundle\MinuteBundle\Entity\Plot;
use Lucca\Bundle\ParameterBundle\Entity\Town;

readonly class PlotManager
{
    public function __construct(
       private EntityManager $em,
       private GeoLocator $geoLocator,
       private RequestStack $requestStack,
    )
    {
    }

    /** Manage location of plot depends on the value of location from
     */
    public function manageLocation(Plot $plot): void
    {
        if ($plot->getLocationFrom() === PLOT::LOCATION_FROM_ADDRESS) {

            /** Use geolocator service to find geocode */
            $this->geoLocator->addGeocodeFromAddress($plot);

            if ($plot->getLatitude() === NULL || $plot->getLongitude() === NULL) {
                $this->requestStack->getSession()->getFlashBag()->add('warning', 'flash.plot.geolocalisationFailed');
            }
        } elseif ($plot->getLocationFrom() === PLOT::LOCATION_FROM_COORDINATES) {
            /** Use geolocator service to find address */
            $address = $this->geoLocator->getAddressFromGeocode($plot->getLatitude(), $plot->getLongitude());

            $addrRoute = '';

            /** add route number */
            if (array_key_exists('addrNumber', $address)) {
                $addrRoute .= $address['addrNumber'] . ' ';
            }

            /** add address name and set in plat */
            if (array_key_exists('addrRoute', $address)) {
                $addrRoute .= $address['addrRoute'];
                $plot->setAddress($addrRoute);
            }

            $town = $this->em->getRepository(Town::class)->findOneBy(array(
                'zipcode' => $address['addrCode'],
                'name' => $address['addrCity']
            ));

            /** If there is no town display error message because it can cause issues */
            if ($town === NULL) {
                $this->requestStack->getSession()->getFlashBag()->add('warning', 'flash.plot.geolocalisationFailedCoordinates');
            } else {
                $plot->setTown($town);
            }
        } else {
            /** Use geolocator service to find city */
            $address = $this->geoLocator->getAddressFromGeocode($plot->getLatitude(), $plot->getLongitude());
            if ($address['addrCode'])
                $town = $this->em->getRepository(Town::class)->findOneBy(array(
                    'zipcode' => $address['addrCode'],
                    'name' => $address['addrCity']
                ));
            else
                $town = $this->em->getRepository(Town::class)->findOneBy(array(
                    'name' => $address['addrCity']
                ));

            /** If there is no town display error message because it can cause issues */
            if ($town === NULL) {
                $this->requestStack->getSession()->getFlashBag()->add('warning', 'flash.plot.geolocalisationFailedManual');
            } else {
                $plot->setTown($town);
            }
        }
    }

    public function getName(): string
    {
        return 'lucca.manager.plot';
    }
}
