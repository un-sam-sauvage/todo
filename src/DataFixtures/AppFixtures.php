<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
	public function __construct(private UserPasswordHasherInterface $passwordHasher)
	{

	}

	public function load(ObjectManager $manager): void
	{
		$userAdmin = new User();
		$userAdmin->setEmail("adminUser@test.com");
		$userAdmin->setRoles(array("ROLE_ADMIN"));
		$userAdmin->setPassword($this->passwordHasher->hashPassword($userAdmin, "123456"));
		$manager->persist($userAdmin);

		$user = new User();
		$user->setEmail("clientUser@test.com");
		$user->setRoles(["ROLE_USER"]);
		$user->setPassword($this->passwordHasher->hashPassword($user, "123456"));
		$manager->persist($user);

		$anonymous = new User();
		$anonymous->setEmail("ano@test.com");
		$anonymous->setPassword($this->passwordHasher->hashPassword($anonymous, "123456"));
		$manager->persist($anonymous);

		for ($i=0; $i < 10; $i++) { 
			$task = new Task();
			$task->setTitle("Task n°". $i);
			$task->setContent("content n°". $i);
			$task->setAuthor((($i % 2 == 0) ? $userAdmin : $user));
			$manager->persist($task);
		}
		$manager->flush();
	}
}
