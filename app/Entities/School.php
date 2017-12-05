<?php

namespace App\Entities;

use Doctrine\ORM\Mapping AS ORM;
use Doctrine\ORM\Mapping\Embedded;

/**
 * @ORM\Entity
 * @ORM\Table(name="schools")
 */
class School
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
    protected $name;

    /**
     * @ORM\Column(type="string")
     */
    protected $motto;

    /**
     * @Embedded(class = "Address", columnPrefix = "address_")
     * @var Address $address Address
     */
    protected $address;

    /**
     * @param String  $name    School name
     * @param String  $motto   School motto
     * @param Address $address School address
     */
    public function __construct(String $name, String $motto, Address $address)
    {
        $this->name = $name;
        $this->motto  = $motto;
        $this->address = $address;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getMotto()
    {
        return $this->motto;
    }

    public function getAddress()
    {
        return $this->address;
    }

}
