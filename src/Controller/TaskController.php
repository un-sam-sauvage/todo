<?php

namespace App\Controller;

use App\Repository\TaskRepository;
use App\Entity\Task;
use App\Form\TaskType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[Route("/task")]
class TaskController extends AbstractController
{

	#[Route("/", name:"task_list")]
	public function index(TaskRepository $taskRepository) : Response
	{
		return $this->render('task/list.html.twig', ['tasks' => $taskRepository->findAll()]);
	}

	#[Route("/create", name:"task_create")]
	public function new(Request $request, TaskRepository $taskRepository) : Response
	{
		$task = new Task();
		$form = $this->createForm(TaskType::class, $task);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
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

	#[Route("/{id}/toggle", name:"task_toggle")]
	public function toggleTaskAction(Task $task, TaskRepository $taskRepository) : Response
	{
		$task->setIsDone(!$task->isDone());
		$taskRepository->save($task, true);

		if ($task->isDone()) {
			$this->addFlash('success', sprintf('Task %s has been finished.', $task->getTitle()));
		} else {
			$this->addFlash('warning', sprintf('Task %s has return to unfinished status', $task->getTitle()));
		}

		return $this->redirectToRoute('task_list', [], Response::HTTP_SEE_OTHER);
	}

	#[Route("/{id}/delete", name:"task_delete")]
	public function deleteTaskAction(Request $request, Task $task, TaskRepository $taskRepository) : Response
	{
		if ($this->isCsrfTokenValid('delete'.$task->getId(), $request->request->get('_token'))) {
			$taskRepository->remove($task, true);
		}

		$this->addFlash('success', 'Task has successfully been deleted.');

		return $this->redirectToRoute('task_list', [], Response::HTTP_SEE_OTHER);
	}
}
