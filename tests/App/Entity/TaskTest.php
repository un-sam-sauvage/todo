<?php
namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase {

	private Task $task;
	private User $user;

	public function setUp() : void {
		error_reporting(0);
		$this->user = new User();

		$this->task = new Task();
		$this->task->setTitle("coucou");
		$this->task->setContent("test");
		$this->task->setAuthor($this->user);
	}
	public function testTitle(): void {
		$this->assertIsString($this->task->getTitle());
	}

	public function testContent(): void {
		$this->assertIsString($this->task->getContent());
	}

	public function testIsDone(): void {
		$this->assertIsBool($this->task->isDone());
	}

	public function testCreated_at(): void {
		$this->assertInstanceOf(DateTimeImmutable::class, $this->task->getCreatedAt());
	}

	public function testAuthor(): void {
		$this->assertInstanceOf(User::class, $this->task->getAuthor());
	}

	public function testCategory(): void {
		$this->assertInstanceOf(Collection::class, $this->task->getCategories());
	}
}