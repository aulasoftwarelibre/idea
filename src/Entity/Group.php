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

use App\Services\Seo\OpenGraphItemInterface;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Entity\File as EmbeddedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Table(name: 'fos_group')]
#[ORM\Entity]
#[Vich\Uploadable]
class Group implements OpenGraphItemInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int|null $id = null;

    #[Gedmo\Slug(fields: ['name'], unique: true)]
    #[ORM\Column(length: 255, unique: true)]
    private string $slug;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    #[Assert\Length(min: 3, max: 180)]
    private string $name;

    #[ORM\Column(length: 32)]
    private string $icon;

    /** @var Collection<int,Idea> */
    #[ORM\OneToMany(mappedBy: 'group', targetEntity: 'App\Entity\Idea', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $ideas;

    /** @var Collection<int, User> */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'groups')]
    private Collection $users;

    #[Gedmo\Timestampable(on: 'update')]
    #[ORM\Column(type: 'datetime')]
    private DateTime|null $updatedAt;

    #[Vich\UploadableField(mapping: 'groups', fileNameProperty: 'image.name', size: 'image.size', mimeType: 'image.mimeType', originalName: 'image.originalName')]
    #[Assert\Image(minWidth: 1200, minHeight: 600, maxRatio: 2, minRatio: 2)]
    private File|UploadedFile|null $imageFile = null;

    #[ORM\Embedded(class: 'Vich\UploaderBundle\Entity\File')]
    private EmbeddedFile $image;

    #[ORM\Column(type: 'string', length: 200, nullable: true)]
    #[Assert\Length(max: 200)]
    #[Assert\NotBlank]
    private string|null $description;

    public function __construct()
    {
        $this->ideas = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    public function __toString(): string
    {
        return (string) $this->name;
    }

    public function getId(): int|null
    {
        return $this->id;
    }

    /** @return Idea[]|Collection */
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

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
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

    public function getName(): string|null
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /** @return Collection|User[] */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (! $this->users->contains($user)) {
            $this->users[] = $user;
            $user->addGroup($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            $user->removeGroup($this);
        }

        return $this;
    }

    public function getUpdatedAt(): DateTime|null
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTime|null $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|UploadedFile|null $image
     */
    public function setImageFile(File|null $image = null): void
    {
        $this->imageFile = $image;

        if (! $image) {
            return;
        }

        // It is required that at least one field changes if you are using doctrine
        // otherwise the event listeners won't be called and the file is lost
        $this->updatedAt = new DateTime();
    }

    public function getImageFile(): File|null
    {
        return $this->imageFile;
    }

    public function setImage(EmbeddedFile $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getImage(): EmbeddedFile|null
    {
        return $this->image;
    }

    public function getDescription(): string|null
    {
        return $this->description;
    }

    public function setDescription(string|null $description): self
    {
        $this->description = $description;

        return $this;
    }
}
