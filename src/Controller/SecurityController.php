<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{

	#[Route("/login", name:"login")]
	public function loginAction(Request $request)
	{
		$authenticationUtils = $this->get('security.authentication_utils');

		$error = $authenticationUtils->getLastAuthenticationError();
		$lastUsername = $authenticationUtils->getLastUsername();

		return $this->render('security/login.html.twig', array(
			'last_username' => $lastUsername,
			'error'         => $error,
		));
	}

	#[Route("/login_check", name:"login_check")]
	public function loginCheck()
	{
		// This code is never executed.
	}

	#[Route("/logout", name:"logout")]
	public function logoutCheck()
	{
		// This code is never executed.
	}
}
