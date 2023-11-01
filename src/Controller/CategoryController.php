<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use App\Security\AccessVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/category', name:'app_category')]
class CategoryController extends AbstractController
{
	#[Route('/', name: '_list', methods: ['GET'])]
	public function index(CategoryRepository $categoryRepository): Response {
		$this->denyAccessUnlessGranted(AccessVoter::ACCESS_ADMIN);

		$categories = $categoryRepository->findAll();
		return $this->render('category/index.html.twig', [
			'categories' => $categories
		]);
	}

	#[Route('/create', name:"_create", methods: ['GET', 'POST'])]
	public function createCategory(Request $request, CategoryRepository $categoryRepository): Response {
		$this->denyAccessUnlessGranted(AccessVoter::ACCESS_LOGGED);

		$category = new Category();
		$form = $this->createForm(CategoryType::class, $category);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			if (in_array("ROLE_ADMIN", $this->getUser()->getRoles())) {
				$category->setIsPublic(true);
			}
			$categoryRepository->save($category, true);
			$this->addFlash("success", 'Category has successfully been created');
			return $this->redirectToRoute('app_category_list', [], Response::HTTP_SEE_OTHER);
		}

		return $this->render('/category/create.html.twig', [
			'form' => $form,
			'category' => $category
		]);
	}

	#[Route('/{id}/edit', name:'_edit', methods: ['GET', 'POST'])]
	public function editCategory(Category $category, Request $request, CategoryRepository $categoryRepository): Response {
		$this->denyAccessUnlessGranted(AccessVoter::ACCESS_ADMIN);

		$form = $this->createForm(CategoryType::class, $category);
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$categoryRepository->save($category, true);
			$this->addFlash('success', 'Category has successfully been edited');

			return $this->redirectToRoute('app_category_list', [], Response::HTTP_SEE_OTHER);
		}
		return $this->render('category/edit.html.twig', [
			'form' => $form,
			'category' => $category
		]);
	}

	#[Route('/{id}', name:'_delete', methods: ['DELETE'])]
	public function deleteCategory (Request $request, Category $category, CategoryRepository $categoryRepository): Response {
		
		$this->denyAccessUnlessGranted(AccessVoter::ACCESS_ADMIN);
		
		$params = json_decode($request->getContent(), true);
		if ($this->isCsrfTokenValid('delete'.$category->getId(), $params["_token"])) {
			$categoryRepository->remove($category, true);
		} else {
			return new JsonResponse([
				"success" => false,
				"msg" => "invalid token"
			]);
		}

		return new JsonResponse([
			"success" => true,
			"msg" => "task has been successfully deleted"
		]);
	}
}
