<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Entity\Beehive;
use App\Repository\TaskRepository;
use App\Repository\BeehiveRepository;
use App\Repository\ApiaryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/task')]
class TaskController extends AbstractController
{
    #[Route('/{id}/showAll', name: 'app_task_index', methods: ['GET'])]
    public function index(TaskRepository $taskRepository, BeehiveRepository $beehiveRepository, ApiaryRepository $apiaryRepository, int $id): Response
    {
        $this->denyAccessUnlessGranted('ROLE_BEEKEEPER');
        return $this->render('task/index.html.twig', [
            'tasks' => $taskRepository->findTasksByBeehiveId($id),
            "beehive" => $beehiveRepository->find($id),
        ]);
    }

    #[Route('/{id}/new', name: 'app_task_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        int $id,
        BeehiveRepository $beehiveRepository,
        EntityManagerInterface $em
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_BEEKEEPER');

        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $beehive = $beehiveRepository->find($id);

            $task->setBeehive($beehive);

            $em->persist($task);
            $em->flush();

            return $this->redirectToRoute('app_task_index', ['id' => $id], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('task/new.html.twig', [
            'task' => $task,
            'form' => $form,
            'idBeehive' => $id
        ]);
    }

    #[Route('/{id}/edit', name: 'app_task_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Task $task, TaskRepository $taskRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_BEEKEEPER');

        $idBeehive = $task->getBeehive()->getId();

        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $taskRepository->save($task, true);

            return $this->redirectToRoute('app_task_index', ['id' => $idBeehive], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('task/edit.html.twig', [
            'taskId' => $task->getBeehive()->getId(),
            'form' => $form,
            'id' => $idBeehive
        ]);
    }

    #[Route('/{id}/delete', name: 'app_task_delete', methods: ['GET'])]
    public function delete(Task $task, TaskRepository $taskRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_BEEKEEPER');

        $idBeehive = $task->getBeehive()->getId();
        $taskRepository->remove($task, true);

        return $this->redirectToRoute('app_task_index', ['id' => $idBeehive], Response::HTTP_SEE_OTHER);
    }
}
