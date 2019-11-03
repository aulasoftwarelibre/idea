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
 * Class Group.
 *
 * @ORM\Entity(repositoryClass="App\Repository\GroupRepository")
 * @ORM\Table(name="fos_group")
 */
class Group extends BaseGroup
{
    /**
     * @var int
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @Groups("idea")
     */
    protected $name;

    /**
     * @var string
     * @ORM\Column(length=32)
     */
    private $icon;

        /**
     * @var string
     * @ORM\Column(length=255, unique=true)
     * @Gedmo\Slug(fields={"name"}, unique=true)
     */
    private $slug;

    /**
     * @var Idea[]|Collection
     * @ORM\OneToMany(targetEntity="App\Entity\Idea", mappedBy="group", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $ideas;

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

    /**
     * @return Group
     */
    public function addIdea(Idea $idea): self
    {
        $this->ideas[] = $idea;

        return $this;
    }

    /**
     * @param Idea $idea
     */
    public function removeIdea(Idea $idea): void
    {
        $this->ideas->removeElement($idea);
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @return string
     */
    public function getIcon(): string
    {
        return (string) $this->icon;
    }

    /**
     * @param string $icon
     * @return Group
     */
    public function setIcon(string $icon): Group
    {
        $this->icon = $icon;

        return $this;
    }
}
