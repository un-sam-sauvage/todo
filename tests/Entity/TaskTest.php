<?php
namespace App\Tests\Entity;

use App\Entity\Task;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase {
	public function testDefault() {
		$task = new Task();
	}
}