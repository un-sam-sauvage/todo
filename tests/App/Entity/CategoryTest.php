<?php

namespace App\Tests\Entity;

use App\Entity\Category;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\TestCase;

class CategoryTest extends TestCase {

	private Category $category;

	public function setUp() : void {
		$this->category = new Category();
		$this->category->setName("Test");
		$this->category->setColor("#000000");
	}

	public function testName(): void {
		$this->assertIsString($this->category->getName());
	}
	
	public function testColor(): void {
		$this->assertIsString($this->category->getColor());
	}

	public function testTask(): void {
		$this->assertInstanceOf(Collection::class, $this->category->getTask());
	}
}