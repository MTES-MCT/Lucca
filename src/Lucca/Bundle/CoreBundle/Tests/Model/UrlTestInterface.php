<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\CoreBundle\Tests\Model;

interface UrlTestInterface
{
    /**
     * Return the status attended when anonymous request
     */
    public function getStatusAnonymous(): ?int;

    /**
     * Configure the status attended when anonymous request
     */
    public function setStatusAnonymous(int $statusAnonymous): self;

    /**
     * Return the status attended when authenticated request
     */
    public function getStatusAuth(): ?int;

    /**
     * Configure the status attended when authenticated request
     */
    public function setStatusAuth(int $statusAuth): self;

    /**
     * Return the method of this request
     */
    public function getMethod(): ?string;

    /**
     * Configure the method of this request
     */
    public function setMethod(string $method): self;

    /**
     * Return the route of this request
     */
    public function getRoute(): ?string;

    /**
     * Configure the route of this request
     */
    public function setRoute(string $route): self;

    /**
     * Return whether this request needs to submit a form
     */
    public function getNeedSubmitForm(): bool;

    /**
     * Configure whether this request has to submit a form
     */
    public function setNeedSubmitForm(bool $needSubmitForm): self;

    /**
     * Return the form data
     */
    public function getFormData(): ?array;
}
