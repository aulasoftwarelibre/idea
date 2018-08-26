<?php

declare(strict_types=1);

/*
 * This file is part of the `idea` project.
 *
 * (c) Aula de Software Libre de la UCO <aulasoftwarelibre@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Form\DataMapper;

use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Exception\RuntimeException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

class GenericDataMapper implements DataMapperInterface
{
    /**
     * @var string
     */
    private $entityClass;

    public function __construct(string $entityClass)
    {
        $this->entityClass = $entityClass;
    }

    /**
     * {@inheritdoc}
     */
    public function mapDataToForms($data, $forms): void
    {
        if (null === $data) {
            return;
        }

        if ($this->entityClass !== \get_class($data)) {
            throw new UnexpectedTypeException($data, $this->entityClass);
        }

        $forms = iterator_to_array($forms);
        $fields = array_keys($forms);

        foreach ($fields as $field) {
            $getter = $this->getFieldAccessor($data, 'get', $field);
            $forms[$field]->setData($data->$getter());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function mapFormsToData($forms, &$data): void
    {
        $forms = iterator_to_array($forms);

        if (null === $data) {
            $data = $this->createInstance($forms);
        }

        if ($this->entityClass !== \get_class($data)) {
            throw new UnexpectedTypeException($data, $this->entityClass);
        }

        foreach ($forms as $field => $form) {
            $setter = $this->getFieldAccessor($data, 'set', $field);
            $data->$setter($form->getData());
        }
    }

    private function createInstance(array $forms): object
    {
        try {
            $instance = new \ReflectionClass($this->entityClass);
        } catch (\ReflectionException $e) {
            throw new \RuntimeException(sprintf(
                'Exception creating instance for \'%s\': %s',
                $this->entityClass,
                $e->getMessage()
            ));
        }

        $parameters = null !== $instance->getConstructor() ? $instance->getConstructor()->getParameters() : [];
        $args = array_map(function (\ReflectionParameter $parameter) use ($forms) {
            $name = $parameter->getName();

            if (!array_key_exists($name, $forms)) {
                throw new RuntimeException(sprintf(
                    'Expected form field \'%s\' to construct class \'%s\' does not exists. Required fields are: %s.',
                    $name,
                    $this->entityClass,
                    implode(', ', array_keys($forms))
                ));
            }

            return $forms[$name]->getData();
        }, $parameters);

        return $instance->newInstanceArgs($args);
    }

    private function getFieldAccessor(object $data, string $prefix, string $field): string
    {
        $accessorName = $prefix . ucfirst($field);

        if (!method_exists($data, $accessorName)) {
            throw new RuntimeException(sprintf(
                'Expected method \'%s\' in class \'%s\' does not exists',
                $accessorName,
                \get_class($data)
            ));
        }

        return $accessorName;
    }
}
