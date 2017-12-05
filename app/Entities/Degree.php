<?php

namespace App\Entities;

use Doctrine\ORM\Mapping AS ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="degrees")
 */
class Degree
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $title;

    /**
    * @ORM\ManyToMany(targetEntity="Scientist", inversedBy="degrees", cascade={"persist"})
    * @ORM\JoinTable(name="degree_scientist")
    * @var ArrayCollection|Scientist[]
    */
    protected $scientists;

    /**
    * @param $title
    * @param $lastname
    */
    public function __construct($title)
    {
        $this->title = $title;

        $this->scientists = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function addScientist(Scientist $scientist)
    {
        if(!$this->scientists->contains($scientist)) {
            $this->scientists->add($scientist);
            $scientist->addDegree($this);
        }

        return $this;
    }

    public function getScientists()
    {
        return $this->scientists;
    }
}
