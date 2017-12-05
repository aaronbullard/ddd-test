<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Entities\School;
use App\Entities\Address;
use App\Entities\Scientist;
use App\Entities\Graduation;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GraduateTest extends TestCase
{
    use RefreshDatabase;

    protected function save($entity)
    {
        app('em')->persist($entity);
        app('em')->flush();
    }

    public function test_hidden_entity_in_a_root_aggregate()
    {
        // Setup
        $address = new Address("121 Blake Rd", NULL, "Annapolis", "MD", "21402");
        $school = new School("University of Zurich", "Eureka!", $address);

        // Execute
        $scientist = new Scientist("Albert", "Einstein");
        $scientist->graduatedFrom($school, 1900);

        // Test
        $schools = $scientist->getSchoolsAttended();
        $this->assertCount(1, $schools);
        $this->assertEquals("University of Zurich", $schools->first()->getName());

        $this->save($scientist);
        $this->assertDatabaseHas('graduations', [
            'scientist_id' => $scientist->getId(),
            'school_id' => $school->getId(),
            'year' => "1900"
        ]);
    }

    public function test_anemic_domain_model_method()
    {
        $address = new Address("121 Blake Rd", NULL, "Annapolis", "MD", "21402");
        $school = new School("University of Zurich", "Eureka!", $address);
        $scientist = new Scientist("Albert", "Einstein");
        $year = 1900;

        $graduation = new Graduation();
        $graduation->setSchool($school);
        $graduation->setScientist($scientist);
        $graduation->setYear($year);

        $this->save($school);
        $this->save($scientist);
        $this->save($graduation);

        $this->assertDatabaseHas('graduations', [
            'scientist_id' => $scientist->getId(),
            'school_id' => $school->getId(),
            'year' => "1900"
        ]);
    }

}
