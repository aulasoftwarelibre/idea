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

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\CommentBundle\Entity\Comment as BaseComment;
use FOS\CommentBundle\Model\SignedCommentInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 */
class Comment extends BaseComment implements SignedCommentInterface
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Thread of this comment.
     *
     * @var Thread
     * @ORM\ManyToOne(targetEntity="App\Entity\Thread")
     */
    protected $thread;

    /**
     * Author of the comment.
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     *
     * @var UserInterface|User
     */
    protected $author;

    /**
     * {@inheritdoc}
     */
    public function setAuthor(UserInterface $author): void
    {
        if (!$author instanceof User) {
            throw new \InvalidArgumentException(sprintf(
                'Expected \'%s\' instance, \'%s\' given.',
                User::class,
                \get_class($author)
            ));
        }

        $this->author = $author;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthor(): ?UserInterface
    {
        return $this->author;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthorName(): string
    {
        if (null === $this->getAuthor()) {
            return 'Anonymous';
        }

        return $this->getAuthor()->getUsername();
    }
}
