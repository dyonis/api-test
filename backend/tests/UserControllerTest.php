<?php

namespace App\Tests;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends WebTestCase
{
    private ?KernelBrowser $client = null;
    private ?EntityManagerInterface $entityManager = null;

    /*public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        $application = new Application(self::$kernel);
         $application->setAutoExit(false);
         $application->run(new ArrayInput([
             'command' => 'doctrine:migrations:migrate',
             '--no-interaction' => true,
             '--env' => 'test',
         ]));
    }*/

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        $this->entityManager->beginTransaction();
    }

    protected function tearDown(): void
    {
        if ($this->entityManager->getConnection()->isTransactionActive()) {
            $this->entityManager->rollback();
        }

        parent::tearDown();
    }

    public function testCreateUser()
    {
        $this->client->request('POST', '/user', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'name' => 'John Doe',
            'email' => 'john.doe@dot.com',
        ]));
        $this->assertEquals(Response::HTTP_CREATED, $this->client->getResponse()->getStatusCode());
    }

    public function testGetUsers()
    {
        $this->client->request('GET', '/users');
        $this->assertResponseIsSuccessful();
        $this->assertJson($this->client->getResponse()->getContent());
    }

    public function testIndex()
    {
        $this->client->request('GET', '/user/1');
        $this->assertResponseIsSuccessful();
        $this->assertJson($this->client->getResponse()->getContent());
    }

    public function testUpdateUser()
    {
        $this->client->request('PUT', '/user/1', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode(['name' => 'Jane Doe']));
        $this->assertResponseIsSuccessful();
    }

    public function testDeleteUser()
    {
        $this->client->request('DELETE', '/user/1');
        $this->assertEquals(Response::HTTP_NO_CONTENT, $this->client->getResponse()->getStatusCode());
    }
}
