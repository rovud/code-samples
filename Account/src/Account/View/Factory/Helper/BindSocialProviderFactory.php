<?php

namespace Mmd\Account\View\Factory\Helper;

use Epos\SocialAuth\Options\ProvidersOptions;
use Interop\Container\ContainerInterface;
use Mmd\Account\View\Helper\BindSocialProvider;
use Mmd\Util\ServiceLocator\ExtractServiceLocatorTrait;

/**
 * Class BindSocialProviderFactory
 *
 * @package Mmd\Account\View\Factory\Helper
 */
class BindSocialProviderFactory
{
    use ExtractServiceLocatorTrait;

    public function __invoke(ContainerInterface $container)
    {
        $sm              = $this->extractServiceLocator($container);
        $providerOptions = $sm->get(ProvidersOptions::class);

        return new BindSocialProvider($providerOptions);
    }
}
