<?php

namespace App\Controller;

use App\Repository\TaskRepository;
use App\Entity\Task;
use App\Form\TaskType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[Route("/task")]
class TaskController extends AbstractController
{

	#[Route("/", name:"task_list")]
	public function index(TaskRepository $taskRepository) : Response
	{
		//si l'utilisateur est connecté
		if ($this->getUser()) {
			if (in_array("ROLE_ADMIN", $this->getUser()->getRoles())) {
				return $this->render('task/list.html.twig', [
					'tasks' => $taskRepository->findAll()
				]);
			} else {
				return $this->render('task/list.html.twig', [
					'tasks' => $taskRepository->findBy(["author" => $this->getUser()])
				]);
			}
		} else {
			return $this->render("security/login.html.twig");
		}
	}

	#[Route("/create", name:"task_create")]
	public function new(Request $request, TaskRepository $taskRepository) : Response
	{
		$task = new Task();
		$form = $this->createForm(TaskType::class, $task);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$task->setAuthor($this->getUser());
			$taskRepository->save($task, true);

			$this->addFlash('success', 'Task has successfully been created.');

			return $this->redirectToRoute('task_list', [], Response::HTTP_SEE_OTHER);
		}

		return $this->render('task/create.html.twig', [
			'form' => $form,
			'task' => $task
		]);
	}

	#[Route("/{id}/edit", name:"task_edit")]
	public function edit(Task $task, Request $request, TaskRepository $taskRepository) : Response
	{
		$form = $this->createForm(TaskType::class, $task);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$taskRepository->save($task, true);

			$this->addFlash('success', 'Task has successfully been edited.');

			return $this->redirectToRoute('task_list', [], Response::HTTP_SEE_OTHER);
		}

		return $this->render('task/edit.html.twig', [
			'form' => $form->createView(),
			'task' => $task,
		]);
	}

	#[Route("/{id}/toggle", name:"task_toggle", methods: ['POST'])]
	public function toggleTaskAction(Task $task, TaskRepository $taskRepository) : Response
	{
		$task->setIsDone(!$task->isDone());
		$taskRepository->save($task, true);

		if ($task->isDone()) {
			$this->addFlash('success', sprintf('Task %s has been finished.', $task->getTitle()));
		} else {
			$this->addFlash('warning', sprintf('Task %s has return to unfinished status', $task->getTitle()));
		}

		return new JsonResponse([
			"msg" => "task has been toggled",
			"newState" => $task->isDone()
		]);
	}

	#[Route("/{id}", name:"task_delete", methods: ['DELETE'])]
	public function deleteTaskAction(Request $request, Task $task, TaskRepository $taskRepository) : Response
	{
		$params = json_decode($request->getContent(), true);
		if ($this->isCsrfTokenValid('delete'.$task->getId(), $params["_token"])) {
			$taskRepository->remove($task, true);
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
