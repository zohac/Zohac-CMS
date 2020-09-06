<?php

namespace App\Tests\Entity;

use App\Entity\User;
use DateTime;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    private $user = [
        'uuid' => 'f436e4ec-9e84-498a-b1e2-16ab207a64dc',
        'email' => 'test@test.com',
        'password' => 'Secr3tP@ssword',
        'token' => 'a-token',
        'tokenValidity' => '',
        'archived' => true,
    ];

    public function setUp(): void
    {
        $this->user['tokenValidity'] = new DateTime();
    }

    public function testValidUser()
    {
        $user = $this->getUser();

        $this->assertEquals($this->user['uuid'], $user->getUuid());
        $this->assertEquals($this->user['email'], $user->getEmail());
        $this->assertEquals($this->user['email'], $user->getUsername());
        $this->assertEquals($this->user['password'], $user->getPassword());
        $this->assertEquals($this->user['token'], $user->getToken());
        $this->assertEquals(
            $this->user['tokenValidity']->format('Y-m-d H:i:s'), $user->getTokenValidity()->format('Y-m-d H:i:s')
        );
        $this->assertContains('ROLE_USER', $user->getRoles());
        $this->assertTrue($user->isArchived());
    }

    public function getUser(): User
    {
        $user = new User();

        foreach ($this->user as $key => $value) {
            $method = 'set'.ucfirst($key);
            $user->$method($value);
        }

        return $user;
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        // avoid memory leaks
        $this->user = null;
    }
}
