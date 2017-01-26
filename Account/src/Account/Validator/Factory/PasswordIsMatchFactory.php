<?php

namespace Mmd\Account\Validator\Factory;

use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Mmd\Account\Validator\PasswordIsMatch;
use Mmd\Util\ServiceLocator\ExtractServiceLocatorTrait;
use Zend\ServiceManager\MutableCreationOptionsInterface;

/**
 * Class PasswordIsMatchFactory
 *
 * @package Mmd\Account\Validator\Factory
 */
class PasswordIsMatchFactory implements MutableCreationOptionsInterface
{

    use ExtractServiceLocatorTrait;

    protected $options = [];

    public function __invoke(ContainerInterface $container)
    {
        $options = $this->options;

        if (isset($options['object_repository']) && is_string($options['object_repository'])) {
            $sm                           = $this->extractServiceLocator($container);
            $objectManager                = $sm->get(EntityManager::class);
            $options['object_repository'] = $objectManager->getRepository($options['object_repository']);
        }

        return new PasswordIsMatch($options);
    }

    /**
     * Set creation options
     *
     * @param  array $options
     *
     * @return void
     */
    public function setCreationOptions(array $options)
    {
        $this->options = $options;
    }
}
