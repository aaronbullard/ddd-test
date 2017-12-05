<?php

namespace App\Entities;

use Doctrine\ORM\Mapping AS ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="graduations")
 */
class Graduation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
    * @ORM\ManyToOne(targetEntity="School", cascade={"persist"})
    * @var School
    */
    protected $school;

    /**
    * @ORM\ManyToOne(targetEntity="Scientist", inversedBy="graduations")
    * @var Scientist
    */
    protected $scientist;

    /**
     * @ORM\Column(type="integer")
     */
    protected $year;


    /**
     *
     * @param School    $school
     * @param Scientist $scientist
     * @param int       $year
     */
    public function __construct(School $school = NULL, Scientist $scientist = NULL, int $year = NULL)
    {
        $this->school = $school;
        $this->scientist = $scientist;
        $this->year = $year;
    }

    public function getSchool()
    {
        return $this->school;
    }

    public function setSchool(School $school)
    {
        $this->school = $school;
        return $this;
    }

    public function setScientist(Scientist $scientist)
    {
        $this->scientist = $scientist;
        return $this;
    }

    public function setYear(int $year)
    {
        $this->year = $year;
        return $this;
    }

}
