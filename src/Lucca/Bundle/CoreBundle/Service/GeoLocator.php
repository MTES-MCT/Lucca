<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\CoreBundle\Service;

use Lucca\Bundle\CoreBundle\Utils\Canonalizer;
use Lucca\Bundle\SettingBundle\Manager\SettingManager;

class GeoLocator
{
    private ?string $google_api_geocode_key;
    private ?string $google_api_is_active;

    public function __construct(
        private readonly Canonalizer $canonalizer,
    )
    {
        $this->google_api_geocode_key = SettingManager::get('setting.map.geocodeKey.name');
        $this->google_api_is_active = SettingManager::get('setting.map.mapActive.name');
    }

    /**
     * Search geometry coordinates with an AddressableTrait entity
     */
    public function addGeocodeFromAddress($entity, ?string $rawAddress = null): bool|object|null
    {
        if (!$this->google_api_is_active) {
            return $entity;
        }

        /** If entity have already his geocode */
        if ($entity->getLatitude() && $entity->getLongitude()) {
            echo('Entity may have his geocode already defined - lat:' . $entity->getLatitude() . ' and long:' . $entity->getLongitude());
        }

        $fullAddress = '';
        if (method_exists($entity, 'getAddress') && $entity->getAddress()) {
            $fullAddress .= $this->canonalizer->slugify($entity->getAddress());
        }

        if (method_exists($entity, 'getAddressCpl') && $entity->getAddressCpl()) {
            $fullAddress .= '+' . $this->canonalizer->slugify($entity->getAddressCpl());
        }

        if (method_exists($entity, 'getZipCode') && $entity->getZipCode()) {
            $fullAddress .= '+' . $this->canonalizer->slugify($entity->getZipCode()->getCode());
        }

        if (method_exists($entity, 'getCity') && $entity->getCity()) {
            $fullAddress .= '+' . $this->canonalizer->slugify($entity->getCity()->getName());
        }

        if (method_exists($entity, 'getDepartment') && $entity->getId() && $entity->getDepartment()) {
            $fullAddress .= '+' . $this->canonalizer->slugify($entity->getDepartment()->getName());
        }

        if (method_exists($entity, 'getRegion') && $entity->getRegion()) {
            $fullAddress .= '+' . $this->canonalizer->slugify($entity->getRegion()->getName());
        }

        if (method_exists($entity, 'getCountry') && $entity->getCountry()) {
            $fullAddress .= '+' . $this->canonalizer->slugify($entity->getCountry()->getName());
        }

        if (method_exists($entity, 'getTown') && $entity->getTown()) {
            $fullAddress .= '+' . $this->canonalizer->slugify($entity->getTown()->getName());
        }

        if (method_exists($entity, 'getPlace') && $entity->getPlace()) {
            $fullAddress .= '+' . $this->canonalizer->slugify($entity->getPlace());
        }

        /** Check if address is not null - else return */
        if (!$fullAddress) {
            return null;
        }

        /**
         * Address Google maps : https://maps.googleapis.com/maps/api/geocode/json?address=' + address + '&key={{ 'setting.map.mapKey.name'|setting  }}
         *
         * ref Google : https://developers.google.com/maps/documentation/geocoding/start
         * ref : https://numa-bord.com/miniblog/php-google-map-api-recuperer-coordonees-gps-depuis-adresse-format-humain/
         *
         *  the address is send with the format : "avenue-de-la-liberte+montpellier"
         */

        /** Create Geocode link to find geometry and others data */
        $googleGeoLink = 'https://maps.google.com/maps/api/geocode/json';
        $googleGeoLink .= '?key=' . $this->google_api_geocode_key . '&address=' . $fullAddress . '&sensor=false&region=fr';

        /** Ask to Google Maps API and get result returned */
        $googleResultsFounded = file_get_contents($googleGeoLink);

        /** Decode Json returned by Google API */
        $googleResultsDecoded = json_decode($googleResultsFounded);

        if ($googleResultsDecoded->status !== 'OK') {
            echo('Results returned by Google API has been rejected - ' . $googleResultsDecoded->status);
        } else {
            /** Take the first result - TODO clean or purpose all results founded */
            $googleFirstResult = $googleResultsDecoded->results[0];

            if (method_exists($entity, 'setLatitude')) {
                $entity->setLatitude($googleFirstResult->geometry->location->lat);
            }

            if (method_exists($entity, 'setLongitude')) {
                $entity->setLongitude($googleFirstResult->geometry->location->lng);
            }
        }

        return $entity;
    }

    /**
     * Search geometry coordinates with an AddressableTrait entity
     */
    public function getGeocodeFromAddress(string $rawAddress): ?array
    {
        if (!$this->google_api_is_active || !$rawAddress) {
            return null;
        }

        $slugAddress = $this->canonalizer->slugify($rawAddress);

        /**
         * Address Google maps : https://maps.googleapis.com/maps/api/geocode/json?address=' + address + '&key={{ 'setting.map.mapKey.name'|setting  }}
         *
         * ref Google : https://developers.google.com/maps/documentation/geocoding/start
         * ref : https://numa-bord.com/miniblog/php-google-map-api-recuperer-coordonees-gps-depuis-adresse-format-humain/
         *
         *  the address is send with the format : "avenue-de-la-liberte+montpellier"
         */

        /** Create Geocode link to find geometry and others data */
        $googleGeoLink = 'https://maps.google.com/maps/api/geocode/json';
        $googleGeoLink .= '?key=' . $this->google_api_geocode_key . '&address=' . $slugAddress . '&sensor=false&region=fr';

        /** Ask to Google Maps API and get result returned */
        $googleResultsFounded = file_get_contents($googleGeoLink);

        /** Decode Json returned by Google API */
        $googleResultsDecoded = json_decode($googleResultsFounded);

        if ($googleResultsDecoded->status !== 'OK') {
            echo('Results returned by Google API has been rejected - ' . $googleResultsDecoded->status);

            return null;
        }

        /** Take the first result - TODO clean or purpose all results founded */
        [$googleFirstResult] = $googleResultsDecoded->results;

        return [
            'latitude' => $googleFirstResult->geometry->location->lat,
            'longitude' => $googleFirstResult->geometry->location->lng,
        ];
    }

    /**
     * Search address from geocode
     */
    public function getAddressFromGeocode($lat, $lng): array|string
    {
        if (!$this->google_api_is_active) {
            return "La google map n'est pas activée";
        }

        $address = [];
        /** Create Geocode link to find geometry and others data */
        $googleGeoLink = 'https://maps.google.com/maps/api/geocode/json';
        $googleGeoLink .= '?latlng=' . $lat . ',' . $lng . '&key=' . $this->google_api_geocode_key . '&sensor=true';

        /** Ask to Google Maps API and get result returned */
        $googleResultsFounded = file_get_contents($googleGeoLink);

        /** Decode Json returned by Google API */
        $googleResultsDecoded = json_decode($googleResultsFounded);

        if ($googleResultsDecoded->status !== 'OK') {
            return ('Results returned by Google API has been rejected - ' . $googleResultsDecoded->status);
        } else {
            /** Take the first result - TODO clean or purpose all results founded */
            foreach ($googleResultsDecoded->results[0]->address_components as $data) {
                if (in_array("street_number", $data->types)) {
                    $address['addrNumber'] = $data->short_name; // Number
                }

                if (in_array("route", $data->types)) {
                    $address['addrRoute'] = $data->short_name; // Route
                }

                if (in_array("postal_code", $data->types)) {
                    $address['addrCode'] = $data->short_name; // Postal code
                }

                if ($googleResultsDecoded->plus_code->compound_code) {
                    $address['addrCity'] = str_replace(",", "", explode(" ", $googleResultsDecoded->plus_code->compound_code)[1]); // City
                    if (!in_array("addrCode", $address)) {
                        $address['addrCode'] = null; // Postal code
                    }
                }
                elseif (in_array("locality", $data->types)) {
                    $address['addrCity'] = $data->short_name; // City
                }
            }
        }

        return $address;
    }

    public function getName(): string
    {
        return 'lucca.utils.geo_locator';
    }
}
