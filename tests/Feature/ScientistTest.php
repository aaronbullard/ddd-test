<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Entities\Degree;
use App\Entities\Theory;
use App\Entities\Scientist;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ScientistTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_creating_a_scientist()
    {
        // Setup
        $scientist = new Scientist('Aaron', 'Bullard');

        // Execute
        app('em')->persist($scientist);
        app('em')->flush();

        // Test
        $this->assertDatabaseHas('scientists', [
            'id' => $scientist->getId()
        ]);
    }

    public function test_adding_a_theory()
    {
        // Setup
        $scientist = new Scientist('Aaron', 'Bullard');

        // Execute
        $scientist->addTheory(new Theory("Hamsters rule the world."));

        app('em')->persist($scientist);
        app('em')->flush();

        // Test
        $this->assertDatabaseHas('scientists', [
            'id' => $scientist->getId()
        ]);
        $this->assertDatabaseHas('theories', [
            'id' => $scientist->getTheories()[0]->getId(),
            'scientist_id' => $scientist->getId()
        ]);
    }

    public function test_creating_a_theory()
    {
        // Setup
        $scientist = new Scientist('Aaron', 'Bullard');

        // Execute
        $scientist->createTheory("Hamsters rule the world.");

        app('em')->persist($scientist);
        app('em')->flush();

        //Test
        $this->assertDatabaseHas('scientists', [
            'id' => $scientist->getId()
        ]);
        $this->assertDatabaseHas('theories', [
            'id' => $scientist->getTheories()[0]->getId(),
            'scientist_id' => $scientist->getId()
        ]);
    }

    public function test_adding_degrees()
    {
        // Setup
        $scientist = new Scientist('Aaron', 'Bullard');
        $degree = new Degree("PhD in Physics");

        // Execute
        $scientist->addDegree($degree);

        app('em')->persist($scientist);
        app('em')->flush();

        //Test
        $this->assertDatabaseHas('scientists', [
            'id' => $scientist->getId()
        ]);
        $this->assertDatabaseHas('degrees', [
            'id' => $degree->getId()
        ]);
        $this->assertDatabaseHas('degree_scientist', [
            'scientist_id' => $scientist->getId(),
            'degree_id' => $degree->getId()
        ]);
    }

    public function test_getting_scientists_of_a_degree()
    {
        $scientists = [
            new Scientist('Aaron', 'Bullard'),
            new Scientist('Ashley', 'Bullard'),
            new Scientist('Susan', 'Bullard')
        ];

        $degree = new Degree("Basket Weaving");

        foreach($scientists as $s){
            $s->addDegree($degree);
            app('em')->persist($s);
        }

        app('em')->flush();

        foreach($scientists as $s){
            $this->assertDatabaseHas('degree_scientist', [
                'scientist_id' => $s->getId(),
                'degree_id' => $degree->getId()
            ]);
        }

        $this->assertCount(3, $degree->getScientists());
        $this->assertEquals("Bullard", $degree->getScientists()[0]->getLastname());
    }

    public function test_many_to_many_hydration()
    {
        // Setup
        $scientist_id = \DB::table('scientists')->insertGetId([
            'firstname' => 'Tom',
            'lastname' => 'Jones'
        ]);

        $degree_id = \DB::table('degrees')->insertGetId([
            'title' => 'Basket Weaving'
        ]);

        \DB::table('degree_scientist')->insert(compact('scientist_id', 'degree_id'));

        $this->assertTrue( \DB::table('scientists')->where('id', $scientist_id)->exists() );
        \DB::commit();

        // Execute
        $degree = app('em')->find(Degree::class, $degree_id);

        // Test
        $this->assertInstanceOf(Degree::class, $degree);
        $this->assertEquals("Tom", $degree->getScientists()[0]->getFirstname());
    }

    public function test_scientist_cannot_have_duplicate_theories()
    {
        // Setup
        $scientist_id = \DB::table('scientists')->insertGetId([
            'firstname' => 'Bob',
            'lastname' => 'Villa'
        ]);

        $theory_id = \DB::table('theories')->insertGetId([
            'scientist_id' => $scientist_id,
            'title' => 'Jeremiah was a bullfrog'
        ]);

        \DB::commit();

        // Execute
        $scientist = app('em')->find(Scientist::class, $scientist_id);
        $theory = app('em')->find(Theory::class, $theory_id);

        $scientist->addTheory($theory);

        app('em')->persist($scientist);
        app('em')->flush();

        $this->assertCount(1, \DB::table('theories')->where('title', 'Jeremiah was a bullfrog')->get());
    }

    public function test_removing_theories_via_scientist()
    {
        // Setup
        $scientist = new Scientist('Albert', 'Einstein');
        $scientist->createTheory("Theory one.");
        $scientist->createTheory("Theory two.");
        $scientist->createTheory("Theory three.");

        app('em')->persist($scientist);
        app('em')->flush();

        // Execute
        $theory = app('em')->find(Theory::class, $scientist->getTheories()->last()->getId());

        $scientist->removeTheory($theory);

        $this->assertCount(2, $scientist->getTheories());

        app('em')->flush();

        // Test
        $this->assertFalse(\DB::table('theories')->where('id', $theory->getId())->exists());
    }

    public function test_add_theory_by_reference()
    {
        $scientist_id = \DB::table('scientists')->insertGetId([
            'firstname' => 'Val',
            'lastname' => 'Markovic'
        ]);
        \DB::commit();

        $scientist = app('em')->getReference(Scientist::class, $scientist_id);

        $theory = new Theory("Where ever you go, there you are.");
        $theory->setScientist($scientist);

        app('em')->persist($theory);
        app('em')->flush();

        $this->assertDatabaseHas('theories', [
            'scientist_id' => $scientist_id,
            'title' => $theory->getTitle()
        ]);
    }
}
