<?php
/**
 * This file is part of the ceo.
 *
 * (c) Aula de Software Libre de la UCO <aulasoftwarelibre@uco.es>
 * (c) Sergio Gómez <sergio@uco.es>
 * (c) Omar Sotillo <i32sofro@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Sonata\UserBundle\Entity\BaseUser;

/**
 * Class User.
 *
 * @ORM\Entity()
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @var int
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var Idea[]
     * @ORM\OneToMany(targetEntity="App\Entity\Idea", mappedBy="owner", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $ideas;

    /**
     * @var Vote[]
     * @ORM\OneToMany(targetEntity="App\Entity\Vote", mappedBy="user", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $votes;

    /**
     * User constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->ideas = new ArrayCollection();
        $this->votes = new ArrayCollection();
    }

    /**
     * @return Idea[]
     */
    public function getIdeas(): array
    {
        return $this->ideas;
    }

    /**
     * @param Idea $idea
     *
     * @return User
     */
    public function addIdea(Idea $idea): self
    {
        $this->ideas[] = $idea;

        return $this;
    }

    /**
     * @param Idea $idea
     */
    public function removeIdea(Idea $idea)
    {
        $this->ideas->removeElement($idea);
    }

    /**
     * @return Vote[]
     */
    public function getVotes(): array
    {
        return $this->votes;
    }

    /**
     * @param Vote $vote
     *
     * @return User
     */
    public function addVote(Vote $vote): self
    {
        $this->votes[] = $vote;

        return $this;
    }

    /**
     * @param Vote $vote
     */
    public function removeVote(Vote $vote)
    {
        $this->votes->removeElement($vote);
    }
}
