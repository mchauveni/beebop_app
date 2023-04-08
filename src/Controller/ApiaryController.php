<?php

namespace App\Controller;

use App\Entity\Apiary;
use App\Form\ApiaryType;
use App\Repository\ApiaryRepository;
use App\Repository\BeehiveRepository;
use App\Repository\BeekeeperRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/apiary')]
class ApiaryController extends AbstractController
{
    #[Route('/', name: 'app_apiary_index', methods: ['GET'])]
    public function index(ApiaryRepository $apiaryRepository, BeekeeperRepository $beekeeperRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_BEEKEEPER');
        $user = $this->getUser();
        $apiaries = $apiaryRepository->findApiariesByBeekeeper($user->getId());

        return $this->render('apiary/index.html.twig', [
            'apiaries' => $apiaries,
            'idBeekeeper' => $this->getUser()->getId(),
        ]);
    }

    #[Route('/new', name: 'app_apiary_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em, BeekeeperRepository $beekeeperRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_BEEKEEPER');
        $apiary = new Apiary();
        $form = $this->createForm(ApiaryType::class, $apiary);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $apiary = $form->getData();
            $apiary
                ->setBeekeeper($this->getUser());
            $em->persist($apiary);
            $em->flush();

            return $this->redirectToRoute('app_apiary_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('apiary/new.html.twig', [
            'apiary' => $apiary,
            'form' => $form,
            'idBeekeeper' => $this->getUser()->getId(),
        ]);
    }

    #[Route('/{id}', name: 'app_beehive_by_apiary_show', methods: ['GET'])]
    public function show(Apiary $apiary, BeehiveRepository $beehiveRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_BEEKEEPER');

        $beehives = $beehiveRepository->findBy([
            'apiary' => $apiary->getId(),
        ]);

        return $this->render('beehive/index.html.twig', [
            'apiary' => $apiary,
            'beehives' => $beehives,
            'idBeekeeper' => $this->getUser()->getId(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_apiary_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Apiary $apiary, ApiaryRepository $apiaryRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_BEEKEEPER');

        $form = $this->createForm(ApiaryType::class, $apiary);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $apiaryRepository->save($apiary, true);

            return $this->redirectToRoute('app_apiary_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('apiary/edit.html.twig', [
            'apiary' => $apiary,
            'form' => $form,
            'idBeekeeper' => $this->getUser()->getId(),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_apiary_delete', methods: ['GET'])]
    public function delete(Apiary $apiary, ApiaryRepository $apiaryRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_BEEKEEPER');

        $apiaryRepository->remove($apiary, true);

        return $this->redirectToRoute('app_apiary_index', [], Response::HTTP_SEE_OTHER);
    }
}
