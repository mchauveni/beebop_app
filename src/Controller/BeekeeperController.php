<?php

namespace App\Controller;

use App\Entity\Apiary;
use App\Entity\Beekeeper;
use App\Form\BeekeeperType;
use App\Repository\BeekeeperRepository;
use App\Repository\ApiaryRepository;
use App\Repository\BeehiveRepository;
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
        return $this->render('beekeeper/index.html.twig', []);
    }

    #[Route('/admin', name: 'admin_dashboard')]
    public function index_admin(
        BeekeeperRepository $beekeeperRepository
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        // récupérer tous les apiculteurs
        $beekeepers = $beekeeperRepository->findBy([], ['id' => 'DESC']);
        return $this->render('admin/index.html.twig', [
            'beekeepers' => $beekeepers,
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
        if ($this->isCsrfTokenValid('delete' . $beekeeper->getId(), $request->request->get('_token'))) {
            $beekeeperRepository->remove($beekeeper, true);
        }

        return $this->redirectToRoute('app_beekeeper_index', [], Response::HTTP_SEE_OTHER);
    }
}
