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

namespace Lucca\MinuteBundle\Controller\Api;

use Lucca\Bundle\MinuteBundle\Entity\MinuteBundle\Entity\Plot;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DashboardController
 *
 * @Route("/")
 * @Security("has_role('ROLE_USER')")
 *
 * @package Lucca\MinuteBundle\Controller\Api
 * @author Alizée Meyer <alizee.m@numeric-wave.eu>
 */
class MinuteController extends Controller
{
    /**
     * Search geocode corresponding to address
     *
     * @Route("/getGeocode", name="lucca_get_geocode", methods={"GET"}, options = { "expose" = true })
     * @Security("has_role('ROLE_USER')")
     *
     * @param Request $p_request
     * @return Response
     */
    public function getGeoCode(Request $p_request)
    {
        $addr = $p_request->get('address');
        $plot = new Plot();
        $plot->setAddress($addr);

        $this->get('lucca.utils.geo_locator')->addGeocodeFromAddress($plot);

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

    /**
     * Get address from coordinates
     *
     * @Route("/getAddress", name="lucca_get_adress", methods={"GET"}, options = { "expose" = true })
     * @Security("has_role('ROLE_USER')")
     *
     * @param Request $p_request
     * @return Response
     */
    public function getAddress(Request $p_request)
    {
        $lat = $p_request->get('lat');
        $lng = $p_request->get('lng');
        $address = $this->get('lucca.utils.geo_locator')->getAddressFromGeocode($lat, $lng);
        if (!$address or !is_array($address)) {
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

    /**
     * Get id city to update the city field
     *
     * @Route("/getTownId", name="lucca_get_townId", methods={"GET"}, options = { "expose" = true })
     * @Security("has_role('ROLE_USER')")
     *
     * @param Request $p_request
     * @return Response
     */
    public function getTownId(Request $p_request)
    {
        $city = $p_request->get('city');


        /** Use regex to get zipCode and name */
        $zipCode = preg_replace('/[^0-9.]+/', '', $city);

        /** Keep only letters, spaces and -  */
        $name = preg_replace('/ *[^ a-zA-ZÀ-ÿ-]+ */', '', $city);

        $em = $this->getDoctrine()->getManager();

        /** First try to find city by using the name */
        $town = $em->getRepository('LuccaParameterBundle:Town')->findOneByName($name);

        /** If we can't find a town try to replace space by - */
        if (!isset($town)) {
            /** Keep only letters */
            $name2 = preg_replace('/ /', '-', $name);
            /** First try to find city by using the name */
            $town = $em->getRepository('LuccaParameterBundle:Town')->findOneByName($name2);
        }

        /** If we can't find a town try to replace - by space */
        if (!isset($town)) {
            /** Keep only letters */
            $name2 = preg_replace('/-/', ' ', $name);
            /** First try to find city by using the name */
            $town = $em->getRepository('LuccaParameterBundle:Town')->findOneByName($name2);
        }

        /** If the town can't be found with name try to find with zipcode */
        if (!isset($town)) {
            $town = $em->getRepository('LuccaParameterBundle:Town')->findByZipCode($zipCode);

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
