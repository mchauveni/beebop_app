<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Apiary;
use App\Entity\Beehive;
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
        $beekeeper = $this->getUser();
        
        return $this->render('beekeeper/index.html.twig', [
            'beekeeper' => $beekeeper,
        ]);
    }

    /*
    *  START route for ADMIN
    */

    #[Route('/admin', name: 'admin_dashboard')]
    public function index_admin(
        BeekeeperRepository $beekeeperRepository,
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        // récupérer tous les apiculteurs
        $beekeepers = $beekeeperRepository->findBy(['verified' => true], ['id' => 'DESC']);

        return $this->render('admin/index.html.twig', [
            'beekeepers' => $beekeepers,
        ]);
    }

    #[Route('/admin/{id}', name: 'admin_beekeeper_show')]
    public function showBeekeeper_admin(
        Beekeeper $beekeeper,
        ApiaryRepository $apiaryRepository,
        BeehiveRepository $beehiveRepository
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $apiaries = $apiaryRepository->findApiariesByBeekeeper($beekeeper->getId());
        return $this->render('admin/show.html.twig', [
            'beekeeper' => $beekeeper,
            'apiaries' => $apiaries,
        ]);
    }

    #[Route('/admin/{id}/awaiting', name: 'admin_beekeeper_await_show')]
    public function showBeekeeperAwait_admin(
        Beekeeper $beekeeper
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $beekeeper = $beekeeper;
        return $this->render('admin/showAwait.html.twig', [
            'beekeeper' => $beekeeper,
        ]);
    }

    #[Route('/admin_validation', name: 'admin_validation')]
    public function validate(
        BeekeeperRepository $beekeeperRepository
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        // récupérer tous les apiculteurs
        $beekeepers = $beekeeperRepository->findBy(['verified' => false], ['id' => 'DESC']);
        return $this->render('admin/validate.html.twig', [
            'beekeepers' => $beekeepers,
        ]);
    }

    #[Route('/admin/{id}/confirm', name: 'admin_beekeeper_confirm')]
    public function confirmBeekeeper_admin(
        Beekeeper $beekeeper,
        EntityManagerInterface $em
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        // récupérer l'apiculteur et le valider
        //$beekeeper = $beekeeper;
        $beekeeper
            ->setVerified(true);
        $em->persist($beekeeper);
        $em->flush();       
        return $this->render('admin/show.html.twig', [
            'beekeeper' => $beekeeper,
        ]);
    }

    /*
    *  END route for ADMIN
    */

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

    #[Route('/{id}', name: 'app_beekeeper_show', methods: ['GET', 'POST'])]
    public function show(Request $request, Beekeeper $beekeeper, BeekeeperRepository $beekeeperRepository): Response
    {
        $beekeeper = $this->getUser();
        return $this->render('beekeeper/show.html.twig', [
            'beekeeper' => $beekeeper,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_beekeeper_delete', methods: ['POST'])]
    public function delete(Request $request, Beekeeper $beekeeper, BeekeeperRepository $beekeeperRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $beekeeper->getId(), $request->request->get('_token'))) {
            $beekeeperRepository->remove($beekeeper, true);
        }

        return $this->redirectToRoute('app_beekeeper_index', [], Response::HTTP_SEE_OTHER);
    }
}
