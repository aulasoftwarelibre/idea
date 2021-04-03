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

use ReflectionClass;
use ReflectionException;
use ReflectionParameter;
use Symfony\Component\Form\Exception\RuntimeException;
use Symfony\Component\Form\Extension\Core\DataMapper\PropertyPathMapper;
use Symfony\Component\Form\FormInterface;
use Traversable;

use function array_key_exists;
use function array_keys;
use function array_map;
use function implode;
use function iterator_to_array;
use function sprintf;

class GenericDataMapper extends PropertyPathMapper
{
    private string $entityClass;

    public function __construct(string $entityClass)
    {
        parent::__construct();

        $this->entityClass = $entityClass;
    }

    /**
     * @psalm-suppress ParamNameMismatch
     * {@inheritdoc}
     */
    public function mapFormsToData($forms, &$data): void
    {
        if ($data === null) {
            $data = $this->createInstance($forms);
        }

        parent::mapFormsToData($forms, $data);
    }

    /**
     * @param FormInterface[]|Traversable $forms A list of {@link FormInterface} instances
     */
    private function createInstance(Traversable $forms): object
    {
        $forms = iterator_to_array($forms);

        try {
            $instance = new ReflectionClass($this->entityClass);
        } catch (ReflectionException $e) {
            throw new RuntimeException(sprintf(
                'Exception creating instance for \'%s\': %s',
                $this->entityClass,
                $e->getMessage()
            ));
        }

        $parameters = $instance->getConstructor() !== null ? $instance->getConstructor()->getParameters() : [];
        $args       = array_map(function (ReflectionParameter $parameter) use ($forms) {
            $name = $parameter->getName();

            if (! array_key_exists($name, $forms)) {
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
}
