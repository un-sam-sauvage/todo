<?php

namespace App\Test\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Router;

class HomeControllerTest extends WebTestCase {
	
	private KernelBrowser|null $client = null;
	private Router|null $urlGenerator = null;
	private UserRepository|null $userRepository = null;
	private User|null $clientUser = null;

	public function setUp(): void {
		$this->client = static::createClient();
		$this->userRepository = $this->client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(User::class);
		$this->urlGenerator = $this->client->getContainer()->get("router.default");
		$this->clientUser = $this->userRepository->findOneByEmail("clientUser@test.com");
	}

	public function testLoginButtonWhenNotLogged() {
		$crawler = $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('app_home'));
		$this->assertSame(1, $crawler->filter('html:contains("Login")')->count());
	}

	public function testLoginButtonWhenLogged() {
		$this->client->loginUser($this->clientUser);
		$crawler = $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('app_home'));
		$this->assertSame(0, $crawler->filter('html:contains("Login")')->count());
	}

	public function testLogoutButtonWhenLogged() {
		$this->client->loginUser($this->clientUser);
		$crawler = $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('app_home'));
		$this->assertSame(1, $crawler->filter('html:contains("Logout")')->count());
	}

	public function testLogoutButtonWhenNotLogged() {
		$crawler = $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('app_home'));
		$this->assertSame(0, $crawler->filter('html:contains("Logout")')->count());
	}
}