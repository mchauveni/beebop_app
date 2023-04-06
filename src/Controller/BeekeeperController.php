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
        $status = $beekeeper->isVerified();
        //dd($status);
        if($status){
            return $this->render('beekeeper/index.html.twig', [
                'beekeeper' => $beekeeper,
            ]);
        }else{
            //$this->denyAccessUnlessGranted($status, false);
            //throw $this->createAccessDeniedException('No access for you!');
            return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);

        }
    }

    /*
    *  START route for ADMIN
    */

    #[Route('/admin', name: 'admin_dashboard')]
    public function index_admin(
        BeekeeperRepository $beekeeperRepository
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
        Beekeeper $beekeeper
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        return $this->render('admin/show.html.twig', [
            'beekeeper' => $beekeeper,
        ]);
    }

    #[Route('/admin/{id}/awaiting', name: 'admin_beekeeper_await_show')]
    public function showBeekeeperAwait_admin(
        Beekeeper $beekeeper,
        ApiaryRepository $apiaryRepository,
        BeehiveRepository $beehiveRepository
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $apiaries = $apiaryRepository->findApiariesByBeekeeper($beekeeper->getId());
        return $this->render('admin/showAwait.html.twig', [
            'beekeeper' => $beekeeper,
            'apiaries' => $apiaries,
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

    #[Route('/{id}/deleteAdmin', name: 'admin_beekeeper_delete')]
    public function deleteBeekeeper_admin(Request $request, Beekeeper $beekeeper, BeekeeperRepository $beekeeperRepository): Response
    {
        $beekeeperRepository->remove($beekeeper, true);
        return $this->redirectToRoute('admin_dashboard', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/deleteAdmin', name: 'admin_beekeeper_validate_delete')]
    public function deleteAwaitingBeekeeper_admin(Request $request, Beekeeper $beekeeper, BeekeeperRepository $beekeeperRepository): Response
    {
        $beekeeperRepository->remove($beekeeper, true);
        return $this->redirectToRoute('admin_validation', [], Response::HTTP_SEE_OTHER);
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

    #[Route('/{id}/show', name: 'app_beekeeper_show', methods: ['GET', 'POST'])]
    public function show(Request $request, Beekeeper $beekeeper, int $id, BeekeeperRepository $beekeeperRepository): Response
    {
        $beekeeper = $beekeeperRepository->find($id);
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
