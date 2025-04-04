<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\SecurityBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Lucca\Bundle\SecurityBundle\Repository\LoginAttemptRepository;

#[ORM\Entity(repositoryClass: LoginAttemptRepository::class)]
#[ORM\Table(name: 'lucca_security_login_attempt')]
class LoginAttempt
{
    #[ORM\Id]
    #[ORM\Column]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    /**
     * Client ip who make the request
     */
    #[ORM\Column(length: 50)]
    private string $requestIp;

    /**
     * List of all ip if request use proxy
     */
    #[ORM\Column(nullable: true)]
    private ?string $clientIps = null;

    /**
     * Client uri who make the request
     */
    #[ORM\Column(length: 250)]
    private string $requestUri;

    /**
     * Date saved by App when request has catch
     */
    #[ORM\Column]
    private DateTime $requestedAt;

    /**
     * Request data - username who is given
     */
    #[ORM\Column(length: 100)]
    private string $username;

    /**
     * Attribute data - _controller who make the request
     */
    #[ORM\Column(nullable: true)]
    private ?string $controllerAsked = null;

    /**
     * Attribute - _firewall_context
     */
    #[ORM\Column(nullable: true)]
    private ?string $firewall = null;

    /**
     * Server data - HTTP_USER_AGENT who make the request
     */
    #[ORM\Column(nullable: true)]
    private ?string $agent = null;

    /**
     * Server data - HTTP_HOST who make the request
     */
    #[ORM\Column(nullable: true)]
    private ?string $host = null;

    /**
     * Server data - System who make the request
     */
    #[ORM\Column(nullable: true)]
    private ?string $system = null;

    /**
     * Server data - SERVER_SOFTWARE who make the request
     */
    #[ORM\Column(nullable: true)]
    private ?string $software = null;

    /**
     * Server data - SERVER_ADDR who make the request
     */
    #[ORM\Column]
    private string $address;

    /**
     * Server data - SERVER_PORT who make the request
     */
    #[ORM\Column(length: 30)]
    private string $port;

    /**
     * Server data - REMOTE_ADDR who make the request
     */
    #[ORM\Column]
    private string $addressRemote;

    /**
     * Server data - REMOTE_PORT who make the request
     */
    #[ORM\Column(length: 30)]
    private string $portRemote;

    /**
     * Server data - REQUEST_TIME who make the request
     */
    #[ORM\Column(length: 200)]
    private string $requestTime;

    #[ORM\Column]
    #[Assert\NotNull(message: 'constraint.not_null')]
    #[Assert\Type(type: 'bool', message: 'constraint.type')]
    private bool $isCleared = true;

    /********************************************************************* Automatic Getters & Setters *********************************************************************/

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRequestIp(): ?string
    {
        return $this->requestIp;
    }

    public function setRequestIp(string $requestIp): self
    {
        $this->requestIp = $requestIp;

        return $this;
    }

    public function getClientIps(): ?string
    {
        return $this->clientIps;
    }

    public function setClientIps(?string $clientIps): self
    {
        $this->clientIps = $clientIps;

        return $this;
    }

    public function getRequestUri(): ?string
    {
        return $this->requestUri;
    }

    public function setRequestUri(string $requestUri): self
    {
        $this->requestUri = $requestUri;

        return $this;
    }

    public function getRequestedAt(): ?DateTime
    {
        return $this->requestedAt;
    }

    public function setRequestedAt(DateTime $requestedAt): self
    {
        $this->requestedAt = $requestedAt;

        return $this;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getControllerAsked(): ?string
    {
        return $this->controllerAsked;
    }

    public function setControllerAsked(?string $controllerAsked): self
    {
        $this->controllerAsked = $controllerAsked;

        return $this;
    }

    public function getFirewall(): ?string
    {
        return $this->firewall;
    }

    public function setFirewall(?string $firewall): self
    {
        $this->firewall = $firewall;

        return $this;
    }

    public function getAgent(): ?string
    {
        return $this->agent;
    }

    public function setAgent(?string $agent): self
    {
        $this->agent = $agent;

        return $this;
    }

    public function getHost(): ?string
    {
        return $this->host;
    }

    public function setHost(?string $host): self
    {
        $this->host = $host;

        return $this;
    }

    public function getSystem(): ?string
    {
        return $this->system;
    }

    public function setSystem(?string $system): self
    {
        $this->system = $system;

        return $this;
    }

    public function getSoftware(): ?string
    {
        return $this->software;
    }

    public function setSoftware(?string $software): self
    {
        $this->software = $software;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getPort(): string
    {
        return $this->port;
    }

    public function setPort(string $port): self
    {
        $this->port = $port;

        return $this;
    }

    public function getAddressRemote(): ?string
    {
        return $this->addressRemote;
    }

    public function setAddressRemote(string $addressRemote): self
    {
        $this->addressRemote = $addressRemote;

        return $this;
    }

    public function getPortRemote(): ?string
    {
        return $this->portRemote;
    }

    public function setPortRemote(string $portRemote): self
    {
        $this->portRemote = $portRemote;

        return $this;
    }

    public function getRequestTime(): ?string
    {
        return $this->requestTime;
    }

    public function setRequestTime(string $requestTime): self
    {
        $this->requestTime = $requestTime;

        return $this;
    }

    public function getIsCleared(): ?bool
    {
        return $this->isCleared;
    }

    public function setIsCleared(bool $isCleared): self
    {
        $this->isCleared = $isCleared;

        return $this;
    }
}
