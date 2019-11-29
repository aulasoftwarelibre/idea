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

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class LogPolicyAdmin extends AbstractAdmin
{
    protected function configureRoutes(RouteCollection $collection): void
    {
        $collection->clearExcept([
            'list',
            'show',
        ]);
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        parent::configureListFields($listMapper);

        $listMapper
            ->addIdentifier('version', null, [
                'route' => ['version' => 'show'],
            ])
            ->add('mandatory', null, [
            ])
            ->add('createAt', null, [
                'format' => 'y/M/d',
            ])
            ->add('User', null)
            ->add('_action', 'actions', [
                'actions' => [
                    'show' => [],
                ],
            ]);
    }

    protected function configureDatagridFilters(DatagridMapper $filterMapper): void
    {
        parent::configureDatagridFilters($filterMapper);

        $filterMapper
            ->add('User', null, [
                'show_filter' => true,
            ])
            ->add('version', null, [
                'show_filter' => true,
            ]);
    }
}
