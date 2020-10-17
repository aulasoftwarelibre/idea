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
use InvalidArgumentException;
use Symfony\Component\Security\Core\User\UserInterface;

use function get_class;
use function sprintf;

/**
 * @ORM\Entity
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 */
class Comment extends BaseComment implements SignedCommentInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @inheritdoc
     */
    protected $id;

    /**
     * Thread of this comment.
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Thread")
     *
     * @var Thread
     * @inheritdoc
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

    public function setAuthor(UserInterface $author): void
    {
        if (! $author instanceof User) {
            throw new InvalidArgumentException(sprintf(
                'Expected \'%s\' instance, \'%s\' given.',
                User::class,
                get_class($author)
            ));
        }

        $this->author = $author;
    }

    public function getAuthor(): ?UserInterface
    {
        return $this->author;
    }

    public function getAuthorName(): string
    {
        if ($this->getAuthor() === null) {
            return 'Anonymous';
        }

        return $this->getAuthor()->getUsername();
    }
}
