<?php

namespace App\Test\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Router;

class TaskControllerTest extends WebTestCase {
	
	private KernelBrowser|null $client = null;
	private Router|null $urlGenerator = null;
	private UserRepository|null $userRepository = null;
	private TaskRepository|null $taskRepository = null;
	private User|null $clientUser = null;
	private User|null $adminUser = null;
	private Task|null $taskUser = null;
	private Task|null $taskAdmin;

	public function setUp(): void {
		$this->client = static::createClient();
		$this->userRepository = $this->client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(User::class);
		$this->taskRepository = $this->client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(Task::class);
		$this->urlGenerator = $this->client->getContainer()->get("router.default");
		$this->clientUser = $this->userRepository->findOneByEmail("clientUser@test.com");
		$this->adminUser = $this->userRepository->findOneByEmail("adminUser@test.com");
		$this->taskUser = $this->taskRepository->findOneBy(["author" => $this->clientUser]);
		$this->taskAdmin = $this->taskRepository->findOneBy(["author" => $this->adminUser]);
	}
	public function test403WhenNotLoggedForSeeingTaskList() {
		$this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('task_list'));
		$this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
	}

	public function test403WhenNotLoggedToCreateTask() {
		$this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('task_create'));
		$this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
	}

	public function testUserSeeingIsTask() {
		$this->client->loginUser($this->clientUser);
		$crawler = $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('task_list'));
		$this->assertSame(1, $crawler->filter('html:contains("'. $this->taskUser->getTitle() .'")')->count());
	}

	public function testUserNotSeeingAdminTask() {
		$this->client->loginUser($this->clientUser);
		$crawler = $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('task_list'));
		$this->assertSame(0, $crawler->filter('html:contains("'. $this->taskAdmin->getTitle() .'")')->count());
	}

	public function testAdminSeeingIsTask() {
		$this->client->loginUser($this->adminUser);
		$crawler = $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('task_list'));
		$this->assertSame(1, $crawler->filter('html:contains("'. $this->taskAdmin->getTitle() .'")')->count());
	}
	
	public function testAdminSeeingUserTask() {
		$this->client->loginUser($this->adminUser);
		$crawler = $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('task_list'));
		$this->assertSame(1, $crawler->filter('html:contains("'. $this->taskUser->getTitle() .'")')->count());
	}

}