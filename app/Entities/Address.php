<?php

namespace App\Entities;

use Doctrine\ORM\Mapping AS ORM;
use Doctrine\ORM\Mapping\Embeddable;


/** @Embeddable */
class Address
{
    /**
     * @ORM\Column(type="string")
     */
    protected $street_1;

    /**
     * @ORM\Column(type="string")
     */
    protected $street_2;

    /**
     * @ORM\Column(type="string")
     */
    protected $city;

    /**
     * @ORM\Column(type="string")
     */
    protected $state;

    /**
     * @ORM\Column(type="string")
     */
    protected $zipcode;


    public function __construct(String $street_1, String $street_2 = NULL, String $city, String $state, String $zipcode)
    {
        static::validateState($state);
        static::validateZipcode($zipcode);

        $this->street_1 = $street_1;
        $this->street_2 = $street_2;
        $this->city = $city;
        $this->state = $state;
        $this->zipcode = $zipcode;
    }

    public function getStreet1()
    {
        return $this->street_1;
    }

    public function getStreet2()
    {
        return $this->street_2;
    }

    public function getCity()
    {
        return $this->city;
    }

    public function getState()
    {
        return $this->state;
    }

    public function getZipcode()
    {
        return $this->zipcode;
    }

    protected static function validateState(String $state)
    {
        if(strlen($state) !== 2){
            throw new \InvalidArgumentException("A state must have be a length of 2.");
        }

        if(!ctype_upper($state)){
            throw new \InvalidArgumentException("A state must contain only uppercase letters.");
        }
    }

    protected static function validateZipcode(String $zipcode)
    {
        if(strlen($zipcode) !== 5){
            throw new \InvalidArgumentException("A zipcode must have be a length of 5.");
        }

        if(!ctype_digit($zipcode)){
            throw new \InvalidArgumentException("A zipcode must only contain integers.");
        }
    }
}
