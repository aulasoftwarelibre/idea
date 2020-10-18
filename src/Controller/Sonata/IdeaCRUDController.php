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

namespace App\Controller\Sonata;

use App\Entity\Idea;
use Sonata\AdminBundle\Controller\CRUDController;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Exception\ModelManagerException;
use Symfony\Component\HttpFoundation\RedirectResponse;

use function assert;

class IdeaCRUDController extends CRUDController
{
    public function batchActionOpen(ProxyQueryInterface $query): RedirectResponse
    {
        $this->admin->checkAccess('edit');

        $selectedIdeas = $query->execute();

        try {
            $modelManager = $this->admin->getModelManager();

            foreach ($selectedIdeas as $selectedIdea) {
                assert($selectedIdea instanceof Idea);
                $selectedIdea->setClosed(false);
                $modelManager->update($selectedIdea);
            }

            $this->addFlash('sonata_flash_success', 'flash_batch_open_success');
        } catch (ModelManagerException $e) {
            $this->addFlash('sonata_flash_error', 'flash_batch_open_error');
        }

        return new RedirectResponse(
            $this->admin->generateUrl('list', [
                'filter' => $this->admin->getFilterParameters(),
            ])
        );
    }

    public function batchActionClose(ProxyQueryInterface $query): RedirectResponse
    {
        $this->admin->checkAccess('edit');

        $selectedIdeas = $query->execute();

        try {
            $modelManager = $this->admin->getModelManager();

            foreach ($selectedIdeas as $selectedIdea) {
                assert($selectedIdea instanceof Idea);
                $selectedIdea->setClosed(true);
                $modelManager->update($selectedIdea);
            }

            $this->addFlash('sonata_flash_success', 'flash_batch_close_success');
        } catch (ModelManagerException $e) {
            $this->addFlash('sonata_flash_error', 'flash_batch_close_error');
        }

        return new RedirectResponse(
            $this->admin->generateUrl('list', [
                'filter' => $this->admin->getFilterParameters(),
            ])
        );
    }
}
