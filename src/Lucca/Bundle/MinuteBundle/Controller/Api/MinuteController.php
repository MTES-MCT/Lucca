<?php
/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MinuteBundle\Controller\Api;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use Lucca\Bundle\CoreBundle\Service\GeoLocator;
use Lucca\Bundle\MinuteBundle\Entity\Plot;
use Lucca\Bundle\ParameterBundle\Entity\Town;

#[Route('/')]
#[IsGranted('ROLE_USER')]
class MinuteController extends AbstractController
{

    public function __construct(
        private readonly GeoLocator             $geoLocator,
        private readonly EntityManagerInterface $entityManager
    )
    {
    }

    #[Route('/getGeocode', name: 'lucca_get_geocode', options: ['expose' => true], methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function getGeoCode(Request $p_request): Response
    {
        $addr = $p_request->get('address');
        $plot = new Plot();
        $plot->setAddress($addr);

        $this->geoLocator->addGeocodeFromAddress($plot);

        if ($plot->getLatitude() === NULL || $plot->getLongitude() === NULL) {
            return new JsonResponse([
                'success' => false,
                'code' => 400,
                'message' => $addr
            ], 400);
        }

        $data['lat'] = $plot->getLatitude();
        $data['lng'] = $plot->getLongitude();

        return new Response(json_encode($data), 200);
    }

    #[Route('/getAddress', name: 'lucca_get_adress', options: ['expose' => true], methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function getAddress(Request $p_request): Response
    {
        $lat = $p_request->get('lat');
        $lng = $p_request->get('lng');
        $address = $this->geoLocator->getAddressFromGeocode($lat, $lng);
        if (empty($address)) {
            return new JsonResponse([
                'success' => false,
                'code' => 400,
                'message' => "Erreur dans la récupération de l'adresse avec les coordonées : latitude " . $lat . " et longitude " . $lng
            ], 400);
        }
        $data['street'] = "";
        if (array_key_exists("addrNumber", $address)) {
            $data['street'] .= $address['addrNumber'] . ' ';
        }
        if (array_key_exists("addrRoute", $address)) {
            $data['street'] .= $address['addrRoute'];
        }

        $data['city'] = $address['addrCity'] . ' ' . $address['addrCode'];
        $data['coords']['lat'] = $lat;
        $data['coords']['lng'] = $lng;
        return new Response(json_encode($data), 200);
    }

    #[Route('/getTownId', name: 'lucca_get_townId', options: ['expose' => true], methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function getTownId(Request $p_request): Response
    {
        $city = $p_request->get('city');


        /** Use regex to get zipCode and name */
        $zipCode = preg_replace('/[^0-9.]+/', '', $city);

        /** Keep only letters, spaces and -  */
        $name = preg_replace('/ *[^ a-zA-ZÀ-ÿ-]+ */', '', $city);

        $em = $this->entityManager;;

        /** First try to find city by using the name */
        $town = $em->getRepository(Town::class)->findOneByName($name);

        /** If we can't find a town try to replace space by - */
        if (!isset($town)) {
            /** Keep only letters */
            $name2 = preg_replace('/ /', '-', $name);
            /** First try to find city by using the name */
            $town = $em->getRepository(Town::class)->findOneByName($name2);
        }

        /** If we can't find a town try to replace - by space */
        if (!isset($town)) {
            /** Keep only letters */
            $name2 = preg_replace('/-/', ' ', $name);
            /** First try to find city by using the name */
            $town = $em->getRepository(Town::class)->findOneByName($name2);
        }

        /** If the town can't be found with name try to find with zipcode */
        if (!isset($town)) {
            $town = $em->getRepository(Town::class)->findByZipCode($zipCode);

            /** We need to test like it and don't use find one because we need to choose what will be send to js code */
            if (count($town) > 1) {

                $data['message'] = "La recherche n'a pas pu aboutir, plusieurs villes ont le même code postal.";
                return new JsonResponse([
                    'success' => false,
                    'code' => 400,
                    'message' => "La recherche n'a pas pu aboutir, plusieurs villes ont le même code postal."
                ], 400);
            } elseif (count($town) == 1) {
                /** If there is only one town keep it outside of an array */
                $town = $town[0];
            }
        }

        /** At this point if we didn't found a town it's mean we can't find with the given information */
        if ($town)
            $data['id'] = $town->getId();
        else {
            $data['message'] = "La recherche n'a pas pu aboutir. Veuillez revoir ou préciser les données saisies";
            return new Response(json_encode($data), 404);
        }

        return new Response(json_encode($data), 200);
    }
}
