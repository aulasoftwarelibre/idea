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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Sonata\UserBundle\Entity\BaseGroup;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GroupRepository")
 * @ORM\Table(name="fos_group")
 */
class Group extends BaseGroup
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @var int
     * @inheritdoc
     */
    protected $id;

    /**
     * @Groups("idea")
     * @var string
     * @inheritdoc
     */
    protected $name;

    /** @ORM\Column(length=32) */
    private string $icon;

    /**
     * @ORM\Column(length=255, unique=true)
     *
     * @Gedmo\Slug(fields={"name"}, unique=true)
     */
    private string $slug;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Idea", mappedBy="group", cascade={"persist", "remove"}, orphanRemoval=true)
     *
     * @var Collection<int,Idea>
     */
    private Collection $ideas;

    /**
     * {@inheritdoc}
     */
    public function __construct($name, array $roles = [])
    {
        parent::__construct($name, $roles);

        $this->ideas = new ArrayCollection();
    }

    /**
     * @return Idea[]|Collection
     */
    public function getIdeas(): Collection
    {
        return $this->ideas;
    }

    public function addIdea(Idea $idea): self
    {
        $this->ideas[] = $idea;

        return $this;
    }

    public function removeIdea(Idea $idea): void
    {
        $this->ideas->removeElement($idea);
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getIcon(): string
    {
        return (string) $this->icon;
    }

    public function setIcon(string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }
}
