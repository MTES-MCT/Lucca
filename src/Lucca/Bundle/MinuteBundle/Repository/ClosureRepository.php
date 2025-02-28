<?php

/*
 * copyright (c) 2025. numeric wave
 *
 * afero general public license (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */

namespace Lucca\MinuteBundle\Repository;

use Doctrine\ORM\EntityRepository;

use Lucca\CoreBundle\Repository\ToggleableRepository;

/**
 * Class ClosureRepository
 *
 * @package Lucca\MinuteBundle\Repository
 * @author Terence <terence@numeric-wave.tech>
 */
class ClosureRepository extends EntityRepository
{
    /** Traits */
    use  ToggleableRepository;
}
