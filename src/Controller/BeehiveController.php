<?php

namespace App\Controller;

use App\Entity\Apiary;
use App\Entity\Beehive;
use App\Form\BeehiveType;
use App\Repository\ApiaryRepository;
use App\Repository\BeehiveRepository;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/beehive')]
class BeehiveController extends AbstractController
{
    #[Route('/{id}/new', name: 'app_beehive_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ApiaryRepository $apiaryRepository, $id, EntityManagerInterface $em): Response
    {
        $idApiary = $id;
        $beehive = new Beehive();
        $form = $this->createForm(BeehiveType::class, $beehive);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $apiary = $apiaryRepository->find($idApiary);
            $beehive->setApiary($apiary);

            $em->persist($beehive);
            $em->flush();

            return $this->redirectToRoute('app_beehive_by_apiary_show', ['id' => $idApiary], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('beehive/new.html.twig', [
            'beehive' => $beehive,
            'form' => $form,
            'idApiary' => $idApiary,
        ]);
    }

    #[Route('/{id}', name: 'app_beehive_show', methods: ['GET'])]
    public function show(Beehive $beehive, ApiaryRepository $apiaryRepository, TaskRepository $taskRepository): Response
    {
        $tasks = $taskRepository->findTasksByBeehiveId($beehive->getId());

        $apiary = $apiaryRepository->findBy([
            'id' => $beehive->getApiary()->getId()
        ]);
        return $this->render('beehive/show.html.twig', [
            'beehive' => $beehive,
            'apiary' => $apiary[0],
            'tasks' => $tasks

        ]);
    }

    #[Route('/{id}/edit', name: 'app_beehive_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Beehive $beehive, BeehiveRepository $beehiveRepository): Response
    {
        $form = $this->createForm(BeehiveType::class, $beehive);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $beehiveRepository->save($beehive, true);
            $idApiary = $beehive->getApiary()->getId();

            return $this->redirectToRoute('app_beehive_by_apiary_show', ['id' => $idApiary], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('beehive/edit.html.twig', [
            'beehive' => $beehive,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_beehive_delete', methods: ['GET'])]
    public function delete(Beehive $beehive, BeehiveRepository $beehiveRepository): Response
    {
        $idApiary = $beehive->getApiary()->getId();
        $beehiveRepository->remove($beehive, true);

        return $this->redirectToRoute('app_beehive_by_apiary_show', ['id' => $idApiary], Response::HTTP_SEE_OTHER);
    }
}
