<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TodolistControllerTest extends WebTestCase
{

    public function testCompleteScenario()
    {
        // Create a new client to browse the application
        $client = static::createClient();

        // Create a new entry in the database
        $crawler = $client->request('GET', '/todolist/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /todolist/");



        $crawler = $client->click($crawler->selectLink('Create a new todolist')->link());

        // Fill in the form and submit it
        $form = $crawler->selectButton('Create')->form(array(
            'appbundle_todolist[description]'  => 'Test',
            'appbundle_todolist[title]'  => 'newtitle',
            'appbundle_todolist[email]'  => 'tomodujmic@gmail.com',
            // ... other fields to fill
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();



        // Check data in the show view
        //$this->assertGreaterThan(0, $crawler->filter('td:contains("Test")')->count(), 'Missing element td:contains("Test")');



        // Edit the entity
        $crawler = $client->click($crawler->selectLink('Edit')->link());


        $form = $crawler->selectButton('Edit')->form(array(
            'appbundle_todolist[description]'  => 'Foo',
            'appbundle_todolist[title]'  => 'Foo',
            'appbundle_todolist[email]'  => 'tomodujmic@gmail.com',
            // ... other fields to fill
        ));


        $client->submit($form);

        $crawler = $client->followRedirect();


        $crawler = $client->click($crawler->selectLink('Back to the list')->link());




        // Check the element contains an attribute with value equals "Foo"
        $this->assertGreaterThan(0, $crawler->filter('td:contains("Foo")')->count(), 'Missing element td:contains("Foo")');
        //$this->assertGreaterThan(0, $crawler->filter('[value="Foo"]')->count(), 'Missing element [value="Foo"]');




        $crawler = $client->click($crawler->selectLink('edit')->link());


        // Delete the entity
        $client->submit($crawler->selectButton('Delete')->form());
        $crawler = $client->followRedirect();

//var_dump($client->getResponse()->getContent());die();


    }


}
