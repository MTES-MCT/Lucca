<?php

return [
    Doctrine\Bundle\DoctrineBundle\DoctrineBundle::class => ['all' => true],
    Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle::class => ['all' => true],
    Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle::class => ['dev' => true, 'test' => true],
    Symfony\Bundle\DebugBundle\DebugBundle::class => ['dev' => true],
    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],
    Symfony\Bundle\MonologBundle\MonologBundle::class => ['all' => true],
    Symfony\Bundle\SecurityBundle\SecurityBundle::class => ['all' => true],
    Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle::class => ['all' => true],
    Symfony\Bundle\TwigBundle\TwigBundle::class => ['all' => true],
    Symfony\Bundle\WebProfilerBundle\WebProfilerBundle::class => ['dev' => true, 'test' => true],

    /** Extra Bundles */
    Knp\Bundle\SnappyBundle\KnpSnappyBundle::class => ['all' => true],
    FOS\JsRoutingBundle\FOSJsRoutingBundle::class => ['all' => true],
    FOS\UserBundle\FOSUserBundle::class => ['all' => true],

    /** Lucca Bundles */
    Lucca\Bundle\AdherentBundle\LuccaAdherentBundle::class => ['all' => true],
    Lucca\Bundle\CoreBundle\LuccaCoreBundle::class => ['all' => true],
    Lucca\Bundle\LogBundle\LuccaLogBundle::class => ['all' => true],
    Lucca\Bundle\MediaBundle\LuccaMediaBundle::class => ['all' => true],
    Lucca\Bundle\SecurityBundle\LuccaSecurityBundle::class => ['all' => true],
    Lucca\Bundle\SettingBundle\LuccaSettingBundle::class => ['all' => true],
    Lucca\Bundle\ThemeDocsUiKitBundle\LuccaThemeDocsUiKitBundle::class => ['all' => true],
    Lucca\Bundle\ThemeStreamDashboardUiKitBundle\LuccaThemeStreamDashboardUiKitBundle::class => ['all' => true],
    Lucca\Bundle\UserBundle\LuccaUserBundle::class => ['all' => true],
];
