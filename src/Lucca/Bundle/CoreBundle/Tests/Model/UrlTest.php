<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\CoreBundle\Tests\Model;

/**
 * Class UrlTest
 *
 * Object used to create some basic Url test with phpunit
 *
 * @package Nw\Bundle\CoreBundle\Tests\Model
 * @author Térence <terence.gusse@nw-group.eu> on 10/05/2022 at 15:24
 */
class UrlTest implements UrlTestInterface
{
    private int $statusAnonymous;
    private int $statusAuth;

    private string $method;
    private string $route;

    private bool $needSubmitForm;
    private array $formData;

    /********************************************************************* Custom functions *********************************************************************/

    /**
     * UrlTest constructor
     * Route is required to create this object
     *
     * @param $route
     * @param null $statusAnonymous
     * @param null $statusAuth
     * @param null $method
     * @param bool $needSubmitForm
     */
    public function __construct($route, $statusAnonymous = null, $statusAuth = null, $method = null, bool $needSubmitForm = false, ?array $formData = [])
    {
        $this->route = $route;

        if ($statusAnonymous)
            $this->statusAnonymous = $statusAnonymous;
        else
            $this->statusAnonymous = 302;

        if ($statusAuth)
            $this->statusAuth = $statusAuth;
        else
            $this->statusAuth = 200;

        if ($method)
            $this->method = $method;
        else
            $this->method = 'GET';

        $this->needSubmitForm = $needSubmitForm;
        $this->formData = $formData;
    }

    /********************************************************************* Automatic Getters & Setters *********************************************************************/

    public function getStatusAnonymous(): ?int
    {
        return $this->statusAnonymous;
    }

    public function setStatusAnonymous(int $statusAnonymous): self
    {
        $this->statusAnonymous = $statusAnonymous;

        return $this;
    }

    public function getStatusAuth(): ?int
    {
        return $this->statusAuth;
    }

    public function setStatusAuth(int $statusAuth): self
    {
        $this->statusAuth = $statusAuth;

        return $this;
    }

    public function getMethod(): ?string
    {
        return $this->method;
    }

    public function setMethod(string $method): self
    {
        $this->method = $method;

        return $this;
    }

    public function getRoute(): ?string
    {
        return $this->route;
    }

    public function setRoute(string $route): self
    {
        $this->route = $route;

        return $this;
    }

    public function getNeedSubmitForm(): bool
    {
        return $this->needSubmitForm;
    }

    public function setNeedSubmitForm(bool $needSubmitForm): self
    {
        $this->needSubmitForm = $needSubmitForm;

        return $this;
    }

    public function getFormData(): ?array
    {
        return $this->formData;
    }

    public function setFormData(?array $formData): self
    {
        $this->formData = $formData;

        return $this;
    }
}

