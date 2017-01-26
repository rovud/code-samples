<?php

namespace Mmd\Account\Validator;

use Doctrine\Common\Persistence\ObjectRepository;
use Epos\UserCore\Entity\UserInterface;
use Epos\UserCore\Service\PasswordService;
use Zend\Validator\AbstractValidator;
use Zend\Validator\Exception;

/**
 * Class PasswordIsMatch
 *
 * @package Mmd\Account\Validator
 */
class PasswordIsMatch extends AbstractValidator
{

    const ERROR_CONTEXT_NOT_FOUND = 'contextNotFound';
    const ERROR_SUBJECT_NOT_FOUND = 'subjectNotFound';
    const ERROR_PASSWORD_MISMATCH = 'passwordMismatch';

    protected $messageTemplates
        = [
            self::ERROR_PASSWORD_MISMATCH => 'Input password is not valid',
            self::ERROR_CONTEXT_NOT_FOUND => 'Context does not contain subject ID',
            self::ERROR_SUBJECT_NOT_FOUND => 'Subject not found',
        ];

    /**
     * @var string
     */
    protected $identifierField = 'id';

    /**
     * @var ObjectRepository
     */
    protected $objectRepository;

    /**
     * Abstract constructor for all validators
     * A validator should accept following parameters:
     *  - nothing f.e. Validator()
     *  - one or multiple scalar values f.e. Validator($first, $second, $third)
     *  - an array f.e. Validator(array($first => 'first', $second => 'second', $third => 'third'))
     *  - an instance of Traversable f.e. Validator($config_instance)
     *
     * @param array|Traversable $options
     */
    public function __construct($options)
    {
        if (isset($options['identifier_field'])) {
            $this->setIdentifierField($options['identifier_field']);
            unset($options['identifier_field']);
        }

        if (!isset($options['object_repository'])) {
            throw new \RuntimeException('Object repository is not defined');
        }

        if (!$options['object_repository'] instanceof ObjectRepository) {
            throw new \RuntimeException(
                sprintf(
                    'Instance of %s expected, got %s',
                    ObjectRepository::class,
                    is_object($options['object_repository'])
                        ? get_class($options['object_repository'])
                        : gettype($options['object_repository'])
                )
            );
        }

        $this->setObjectRepository($options['object_repository']);
        unset($options['object_repository']);

        parent::__construct($options);
    }

    /**
     * @return string
     */
    public function getIdentifierField()
    {
        return $this->identifierField;
    }

    /**
     * @param string $identifierField
     */
    public function setIdentifierField($identifierField)
    {
        $this->identifierField = $identifierField;
    }

    /**
     * @return ObjectRepository
     */
    public function getObjectRepository()
    {
        return $this->objectRepository;
    }

    /**
     * @param ObjectRepository $objectRepository
     */
    public function setObjectRepository(ObjectRepository $objectRepository)
    {
        $this->objectRepository = $objectRepository;
    }

    /**
     * Returns true if and only if $value meets the validation requirements
     *
     * If $value fails validation, then this method returns false, and
     * getMessages() will return an array of messages that explain why the
     * validation failed.
     *
     * @param  mixed $value
     *
     * @return bool
     * @throws Exception\RuntimeException If validation of $value is impossible
     */
    public function isValid($value)
    {
        $this->setValue($value);

        $context = func_num_args() === 2 ? func_get_arg(1) : [];

        if (!isset($context[$this->identifierField])) {
            $this->error(static::ERROR_CONTEXT_NOT_FOUND);

            return false;
        }

        $user = $this->objectRepository->find($context[$this->identifierField]);

        if (!$user instanceof UserInterface) {
            $this->error(static::ERROR_SUBJECT_NOT_FOUND);

            return false;
        }

        if (!PasswordService::verify($value, $user->getPassword())) {
            $this->error(static::ERROR_PASSWORD_MISMATCH);

            return false;
        }

        return true;
    }
}
