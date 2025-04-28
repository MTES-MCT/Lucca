<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\DepartmentBundle\Voter;

use Lucca\Bundle\AdherentBundle\Finder\AdherentFinder;
use Lucca\Bundle\DepartmentBundle\Entity\Department;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Class departmentAccessVoter
 *
 * @package Sparky\Bundle\CoreBundle\Voter
 */
class departmentAccessVoter extends Voter
{
    /** Define a type who need to be checked
     * - call in each Controller who need to use this Voter
     */
    const CHECK_DEPARTMENT = 'security.check.department';

    private ?string $sparkyUnitTestDepCode;

    /**
     * ScopeCompanyVoter constructor
     *
     * @param RequestStack $requestStack
     * @param AdherentFinder $adherentFinder
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(
        private readonly RequestStack                  $requestStack,
        private readonly AdherentFinder                 $adherentFinder,
        private readonly AuthorizationCheckerInterface $authorizationChecker,
        private readonly ParameterBagInterface         $parameterBag
    )
    {
        $this->sparkyUnitTestDepCode = $this->parameterBag->get('lucca_core.lucca_unit_test_dep_code');
    }

    /**
     * Your job is to determine if your voter should vote on the attribute/subject combination.
     * If you return true, voteOnAttribute() will be called.
     *
     * When isGranted() (or denyAccessUnlessGranted()) is called
     * @doc https://symfony.com/doc/5.4/security/voters.html#creating-the-custom-voter
     *
     * @param string $attribute
     * @param mixed $subject
     * @return bool
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        if ($attribute === self::CHECK_DEPARTMENT)
            return true;

        return false;
    }

    /**
     * If you return true from supports(), then this method is called
     * @doc https://symfony.com/doc/5.4/security/voters.html#creating-the-custom-voter
     *
     * @param string $attribute
     * @param mixed $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $isAdmin = $this->authorizationChecker->isGranted('ROLE_ADMIN');

        /** If in case of not in unit test */
        if ($this->sparkyUnitTestDepCode !== 'null')
            return true;

        $masterRequest = $this->requestStack->getMainRequest();
        $subDomainKey = $masterRequest->getSession()->get('subDomainKey');

        /** Check if subDomainKey exists */
        if ($subDomainKey === null)
            return false;

        $currentAdherent = $this->adherentFinder->whoAmI();

        if (!$isAdmin) {
            if ($currentAdherent === null)
                return false;

            if ($currentAdherent->getDepartments()->isEmpty())
                return false;

            /** Check if current adherent has a department */
            /** @var Department $department */
            foreach ($currentAdherent->getDepartments() as $department) {
                if ($department->getCode() === $subDomainKey)
                    return true;
            }
        } else {
            return true;
        }

        return false;
    }
}
