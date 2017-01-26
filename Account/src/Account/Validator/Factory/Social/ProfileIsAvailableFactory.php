<?php

namespace Mmd\Account\Validator\Factory\Social;

use Epos\SocialAuth\Service\ProviderService;
use Interop\Container\ContainerInterface;
use Mmd\Account\Validator\Social\ProfileIsAvailable;
use Mmd\Util\ServiceLocator\ExtractServiceLocatorTrait;

/**
 * Class ProfileIsAvailableFactory
 *
 * @package Mmd\Account\Validator\Factory\Social
 */
class ProfileIsAvailableFactory
{
    use ExtractServiceLocatorTrait;

    public function __invoke(ContainerInterface $container)
    {
        $sm              = $this->extractServiceLocator($container);
        $providerService = $sm->get(ProviderService::class);

        return new ProfileIsAvailable($providerService);
    }
}
