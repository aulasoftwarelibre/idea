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

use Doctrine\ORM\Mapping as ORM;
use Sonata\UserBundle\Entity\BaseGroup;

/**
 * Class Group.
 *
 * @ORM\Entity()
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
     * @var Idea[]
     * @ORM\OneToMany(targetEntity="App\Entity\Idea", mappedBy="group", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $ideas;

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
    public function removeIdea(Idea $idea)
    {
        $this->ideas->removeElement($idea);
    }
}
