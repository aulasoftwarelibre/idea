<?php

/*
 * This file is part of the ceo project.
 *
 * (c) Aula de Software Libre de la UCO <aulasoftwarelibre@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Twig\Extension;

use App\Entity\Idea;
use App\Entity\Thread;
use App\Repository\ThreadRepository;
use Symfony\Component\Translation\TranslatorInterface;

class CeoExtension extends \Twig_Extension
{
    /**
     * @var ThreadRepository
     */
    private $threadRepository;
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(ThreadRepository $threadRepository, TranslatorInterface $translator)
    {
        $this->threadRepository = $threadRepository;
        $this->translator = $translator;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('idea_count_comments', [$this, 'getIdeaCountComments']),
        ];
    }

    public function getIdeaCountComments(Idea $idea)
    {
        $ideaId = $idea->getId();
        $thread = $this->threadRepository->find($ideaId);

        if (!$thread instanceof Thread) {
            $count = 0;
        } else {
            $count = $thread->getNumComments();
        }

        return $this->translator->transChoice('idea_num_comments', $count, ['%count%' => $count]);
    }
}
