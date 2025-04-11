<?php

namespace Lucca\Bundle\DepartmentBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;

use Lucca\Bundle\DepartmentBundle\Entity\Department;

readonly class DepartmentService
{
    private ?string $luccaUnitTestDepCode;

    public function __construct(
        private EntityManagerInterface $em,
        private RequestStack $requestStack,
        private ParameterBagInterface $parameterBag
    )
    {
        $this->luccaUnitTestDepCode = $this->parameterBag->get('lucca_core.lucca_unit_test_dep_code');
    }

    /** Get current department */
    public function getDepartmentSelected(): ?Department
    {
        /** If in case of unit test get the department by code */
        if ($this->luccaUnitTestDepCode !== 'null') {
            return $this->em->getRepository(Department::class)->findOneBy(['code' => $this->luccaUnitTestDepCode]);
        }

        $subDomainKey = $this->requestStack->getCurrentRequest()->getSession()->get('subDomainKey');

        return $this->em->getRepository(Department::class)->findOneBy(['code' => $subDomainKey]);
    }
}
