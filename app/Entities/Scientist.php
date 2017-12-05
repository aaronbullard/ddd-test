<?php

namespace App\Entities;

use Doctrine\ORM\Mapping AS ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="scientists")
 */
class Scientist
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
    protected $firstname;

    /**
     * @ORM\Column(type="string")
     */
    protected $lastname;

    /**
    * @ORM\OneToMany(targetEntity="Theory", mappedBy="scientist", cascade={"persist"}, orphanRemoval=true)
    * @var ArrayCollection|Theory[]
    */
    protected $theories;

    /**
    * @ORM\ManyToMany(targetEntity="Degree", inversedBy="scientists", cascade={"persist"})
    * @ORM\JoinTable(name="degree_scientist")
    * @var ArrayCollection|Degree[]
    */
    protected $degrees;

    /**
    * @ORM\OneToMany(targetEntity="Graduation", mappedBy="scientist", cascade={"persist"}, orphanRemoval=true)
    * @var ArrayCollection|Graduation[]
    */
    protected $graduations;

    /**
    * @param $firstname
    * @param $lastname
    */
    public function __construct($firstname, $lastname)
    {
        $this->firstname = $firstname;
        $this->lastname  = $lastname;

        $this->theories     = new ArrayCollection;
        $this->degrees      = new ArrayCollection;
        $this->graduations  = new ArrayCollection;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getFirstname()
    {
        return $this->firstname;
    }

    public function getLastname()
    {
        return $this->lastname;
    }

    public function addTheory(Theory $theory)
    {
        if(!$this->theories->contains($theory)) {
            $this->theories->add($theory);
            $theory->setScientist($this);
        }

        return $this;
    }

    public function createTheory(String $theoryTitle)
    {
        return $this->addTheory(new Theory($theoryTitle));
    }

    public function getTheories()
    {
        return $this->theories;
    }

    public function removeTheory(Theory $theory)
    {
        $this->theories->removeElement($theory);

        return $this;
    }

    public function addDegree(Degree $degree)
    {
        if(!$this->degrees->contains($degree)) {
            $this->degrees->add($degree);
            $degree->addScientist($this);
        }

        return $this;
    }

    public function getDegrees()
    {
        return $this->degrees;
    }

    public function graduatedFrom(School $school, int $year)
    {
        $graduation = new Graduation($school, $this, $year);

        if(!$this->graduations->contains($graduation)) {
            $this->graduations->add($graduation);
        }

        return $this;
    }

    public function getSchoolsAttended()
    {
        return $this->graduations->map(function($graduation){
            return $graduation->getSchool();
        });
    }
}
