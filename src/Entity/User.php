<?php

/*
 * This file is part of the ceo project.
 *
 * (c) Aula de Software Libre de la UCO <aulasoftwarelibre@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Sonata\UserBundle\Entity\BaseUser;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class User.
 *
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    const STUDENT = 'student';
    const STAFF = 'staff';
    const TEACHER = 'teacher';
    const EXTERNAL = 'external';

    /**
     * @var int
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string|null
     * @ORM\Column(length=32, nullable=true)
     * @Assert\Choice(callback="getCollectives")
     * @Assert\NotBlank()
     */
    protected $collective;

    /**
     * @var string|null
     * @ORM\Column(length=255, nullable=true)
     * @Assert\Length(max="255")
     */
    protected $area;

    /**
     * @var Degree|null
     * @ORM\ManyToOne(targetEntity="App\Entity\Degree")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    private $degree;

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
     * @param User $user
     *
     * @return bool
     */
    public function equalsTo(User $user)
    {
        return $this->getId() === $user->getId();
    }

    /**
     * @return array
     */
    public static function getCollectives(): array
    {
        return [
            'Estudiante' => static::STUDENT,
            'PDI' => static::TEACHER,
            'PAS' => static::STAFF,
            'Otros' => static::EXTERNAL,
        ];
    }

    /**
     * @return string|null
     */
    public function getCollective(): ?string
    {
        return $this->collective;
    }

    /**
     * @param string|null $collective
     *
     * @return User
     */
    public function setCollective(?string $collective): User
    {
        $this->collective = $collective;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     * @param null|string $area
     *
     * @return User
     */
    public function setArea($area)
    {
        $this->area = $area;

        return $this;
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
        $vote->setUser($this);
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

    /**
     * @return Degree|null
     */
    public function getDegree(): ?Degree
    {
        return $this->degree;
    }

    /**
     * @param Degree|null $degree
     *
     * @return User
     */
    public function setDegree(?Degree $degree): User
    {
        $this->degree = $degree;

        return $this;
    }
}
