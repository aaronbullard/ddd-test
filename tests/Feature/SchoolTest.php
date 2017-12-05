<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Entities\School;
use App\Entities\Address;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SchoolTest extends TestCase
{
    use RefreshDatabase;

    protected function save($entity)
    {
        app('em')->persist($entity);
        app('em')->flush();
    }

    public function test_embedding_an_address()
    {
        $address = new Address("121 Blake Rd", NULL, "Annapolis", "MD", "21402");
        $school = new School("United States Naval Academy", "Go Navy, Beat Army!", $address);

        $this->save($school);

        $this->assertDatabaseHas('schools', [
            'name' => $school->getName(),
            'address_city' => $school->getAddress()->getCity()
        ]);
    }

    /** @test */
    public function a_state_must_be_an_abbreviation()
    {
        $this->expectException(\InvalidArgumentException::class);
        $address = new Address("Street 1", NULL, "City", "NORTH CAROLINA", "28412");
    }

    /** @test */
    public function a_state_must_be_all_uppercase()
    {
        $this->expectException(\InvalidArgumentException::class);
        $address = new Address("Street 1", NULL, "City", "nc", "28412");
    }

    /** @test */
    public function a_zipcode_must_have_a_length_of_5()
    {
        $this->expectException(\InvalidArgumentException::class);
        $address = new Address("Street 1", NULL, "City", "NC", "28412-1234");
    }

    /** @test */
    public function a_zipcode_must_contain_only_digits()
    {
        $this->expectException(\InvalidArgumentException::class);
        $address = new Address("Street 1", NULL, "City", "NC", "wrong");
    }
}
