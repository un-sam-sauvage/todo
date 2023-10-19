<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/users')]
class UserController extends AbstractController
{
	#[Route('/', name:'user_list', methods: ['GET'])]
	public function index(UserRepository $userRepository) : Response
	{
		if ($this->getUser() && in_array("ROLE_ADMIN", $this->getUser()->getRoles())) {
			return $this->render('user/list.html.twig', ['users' => $userRepository->findAll()]);
		} else {
			return $this->render('security/login.html.twig');
		}
	}

	#[Route('/create', name:'user_create')]
	public function new(Request $request, UserRepository $userRepository) : Response
	{
		$user = new User();
		$form = $this->createForm(UserType::class, $user);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {

			$userRepository->save($user, true);
			$this->addFlash('success', "User successfully created.");

			return $this->redirectToRoute('user_list', [], Response::HTTP_SEE_OTHER);
		}

		return $this->render('user/create.html.twig', [
			'form' => $form,
			'user' => $user
		]);
	}

	#[Route('/{id}/edit', name:'user_edit', methods: ['GET', 'POST'])]
	public function edit(User $user, Request $request, UserRepository $userRepository) : Response
	{
		$form = $this->createForm(UserType::class, $user);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$userRepository->save($user, true);

			$this->addFlash('success', "User successfully edited.");

			return $this->redirectToRoute('user_list');
		}
		return $this->render('user/edit.html.twig', [
			'form' => $form,
			'user' => $user
		]);
	}

	#[Route('/{id}/delete', name: 'user_delete', methods: ['POST'])]
	public function delete(Request $request, User $user, UserRepository $userRepository): Response
	{
		if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
			$userRepository->remove($user, true);
		}

		return $this->redirectToRoute('user_list', [], Response::HTTP_SEE_OTHER);
	}
}
