<?php

namespace App\Controller;

use App\Entity\Beekeeper;
use App\Form\BeekeeperType;
use App\Repository\BeekeeperRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/beekeeper')]
class BeekeeperController extends AbstractController
{
    #[Route('/', name: 'app_beekeeper_index', methods: ['GET'])]
    public function index(BeekeeperRepository $beekeeperRepository): Response
    {
        return $this->render('beekeeper/index.html.twig', [
            'beekeepers' => $beekeeperRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_beekeeper_new', methods: ['GET', 'POST'])]
    public function new(Request $request, BeekeeperRepository $beekeeperRepository): Response
    {
        $beekeeper = new Beekeeper();
        $form = $this->createForm(BeekeeperType::class, $beekeeper);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $beekeeperRepository->save($beekeeper, true);

            return $this->redirectToRoute('app_beekeeper_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('beekeeper/new.html.twig', [
            'beekeeper' => $beekeeper,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_beekeeper_show', methods: ['GET'])]
    public function show(Beekeeper $beekeeper): Response
    {
        return $this->render('beekeeper/show.html.twig', [
            'beekeeper' => $beekeeper,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_beekeeper_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Beekeeper $beekeeper, BeekeeperRepository $beekeeperRepository): Response
    {
        $form = $this->createForm(BeekeeperType::class, $beekeeper);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $beekeeperRepository->save($beekeeper, true);

            return $this->redirectToRoute('app_beekeeper_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('beekeeper/edit.html.twig', [
            'beekeeper' => $beekeeper,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_beekeeper_delete', methods: ['POST'])]
    public function delete(Request $request, Beekeeper $beekeeper, BeekeeperRepository $beekeeperRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$beekeeper->getId(), $request->request->get('_token'))) {
            $beekeeperRepository->remove($beekeeper, true);
        }

        return $this->redirectToRoute('app_beekeeper_index', [], Response::HTTP_SEE_OTHER);
    }
}
