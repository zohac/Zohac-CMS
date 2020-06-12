<?php

namespace App\Tests\Dto\User;

use App\Dto\User\UserDto;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintViolation;

class UserDtoTest extends KernelTestCase
{
    public function getUserDto(): UserDto
    {
        $userDto = new UserDto();
        $userDto->uuid = 'f436e4ec-9e84-498a-b1e2-16ab207a64dc';
        $userDto->email= 'test@test.com';
        $userDto->password = 'Secr3tP@ssword';
        $userDto->token = 'a-token';
        $userDto->tokenValidity = (new \DateTime())->format('Y-m-d H:i:s');

        return $userDto;
    }

    public function assertHasErrors(UserDto $userDto, int $number = 0)
    {
        self::bootKernel();
        $errors = self::$container->get('validator')->validate($userDto);
        $messages = [];
        /** @var ConstraintViolation $error */
        foreach($errors as $error) {
            $messages[] = $error->getPropertyPath() . ' => ' . $error->getMessage();
        }
        $this->assertCount($number, $errors, implode(', ', $messages));
    }

    public function testValidUserDto()
    {
        $this->assertHasErrors($this->getUserDto(), 0);
    }

    public function testInvalidUuid()
    {
        $userDto = $this->getUserDto();
        $userDto->uuid = 'invalid-Uuid';
        $this->assertHasErrors($userDto, 1);
    }

    public function testInvalidMail()
    {
        $userDto = $this->getUserDto();
        $userDto->email = 'invalid-Mail';
        $this->assertHasErrors($userDto, 1);
    }

    public function testBlankUuid()
    {
        $userDto = $this->getUserDto();
        $userDto->uuid = '';
        $this->assertHasErrors($userDto, 1);
    }

    public function testBlankEmail()
    {
        $userDto = $this->getUserDto();
        $userDto->email = '';
        $this->assertHasErrors($userDto, 1);
    }

    public function testBlankPassword()
    {
        $userDto = $this->getUserDto();
        $userDto->password = '';
        $this->assertHasErrors($userDto, 0);
    }

    public function testInvalidPassword()
    {
        $userDto = $this->getUserDto();
        $userDto->password = 123;
        $this->assertHasErrors($userDto, 1);
    }

    public function testValidToken()
    {
        $this->assertHasErrors($this->getUserDto(), 0);
    }

    public function testInvalidToken()
    {
        $userDto = $this->getUserDto();
        $userDto->token = 123;
        $this->assertHasErrors($userDto, 1);
    }

    public function testBlankToken()
    {
        $userDto = $this->getUserDto();
        $userDto->token = '';
        $this->assertHasErrors($userDto, 0);
    }

    public function testInvalidTokenValidity()
    {
        $userDto = $this->getUserDto();
        $userDto->tokenValidity = 'invalid-datetime';
        $this->assertHasErrors($userDto, 1);
    }

    public function testBlankTokenValidity()
    {
        $userDto = $this->getUserDto();
        $userDto->tokenValidity = '';
        $this->assertHasErrors($userDto, 0);
    }
}