<?php

namespace Mmd\Account\Controller\Factory;

use Epos\SocialAuth\Service\ProviderService;
use Epos\UserCore\Service\UserService;
use Interop\Container\ContainerInterface;
use Mmd\Account\Controller\ProfileController;
use Mmd\Account\Service\SocialAttachmentService;
use Mmd\Util\ServiceLocator\ExtractServiceLocatorTrait;

/**
 * Class ProfileControllerFactory
 *
 * @package Mmd\Account\Controller\Factory
 */
class ProfileControllerFactory
{

    use ExtractServiceLocatorTrait;

    public function __invoke(ContainerInterface $container)
    {
        $sm = $this->extractServiceLocator($container);

        $formManager   = $sm->get('FormElementManager');
        $userService   = $sm->get(UserService::class);
        $attachService = $sm->get(SocialAttachmentService::class);

        return new ProfileController($formManager, $userService, $attachService);
    }
}
