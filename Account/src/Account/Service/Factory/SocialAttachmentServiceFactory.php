<?php

namespace Mmd\Account\Service\Factory;

use Epos\SocialAuth\Service\ProviderService;
use Interop\Container\ContainerInterface;
use Mmd\Account\Service\SocialAttachmentService;
use Mmd\Account\Validator\Social\ProfileValidator;

/**
 * Class SocialAttachmentServiceFactory
 *
 * @package Mmd\Account\Service\Factory
 */
class SocialAttachmentServiceFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $validator       = $container->get('ValidatorManager')->get(ProfileValidator::class);
        $providerService = $container->get(ProviderService::class);

        return new SocialAttachmentService($providerService, $validator);
    }
}
