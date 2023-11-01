<?php

namespace App\Tests\Entity;

use App\Entity\User;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase {
	private User $user;

	public function setUp() : void {
		$this->user = new User();
		$this->user->setEmail("test@gmail.com");
		$this->user->setPassword("123");
		$this->user->setRoles(["ROLE_ADMIN", "ROLE_USER"]);
		$this->user->setIsVerified(false);
	}

	public function testEmail(): void {
		$this->assertIsString($this->user->getEmail());
	}

	public function testPassword(): void {
		$this->assertIsString($this->user->getPassword());
	}

	public function testRoles(): void {
		$this->assertIsArray($this->user->getRoles());
	}

	public function testIsVerified(): void {
		$this->assertIsBool($this->user->isVerified());
	}

	public function testTask(): void {
		$this->assertInstanceOf(Collection::class, $this->user->getTasks());
	}
}