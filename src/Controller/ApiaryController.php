<?php

namespace App\Controller;

use App\Entity\Apiary;
use App\Form\ApiaryType;
use App\Repository\ApiaryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/apiary')]
class ApiaryController extends AbstractController
{
    #[Route('/', name: 'app_apiary_index', methods: ['GET'])]
    public function index(ApiaryRepository $apiaryRepository): Response
    {
        $user = $this->getUser();
        $apiaries = $apiaryRepository->findApiariesByBeekeeper($user->getId());

        return $this->render('apiary/index.html.twig', [
            'apiaries' => $apiaries,

        ]);
    }

    #[Route('/new', name: 'app_apiary_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ApiaryRepository $apiaryRepository): Response
    {
        $apiary = new Apiary();
        $form = $this->createForm(ApiaryType::class, $apiary);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $apiaryRepository->save($apiary, true);

            return $this->redirectToRoute('app_apiary_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('apiary/new.html.twig', [
            'apiary' => $apiary,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_apiary_show', methods: ['GET'])]
    public function show(Apiary $apiary): Response
    {
        return $this->render('apiary/show.html.twig', [
            'apiary' => $apiary,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_apiary_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Apiary $apiary, ApiaryRepository $apiaryRepository): Response
    {
        $form = $this->createForm(ApiaryType::class, $apiary);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $apiaryRepository->save($apiary, true);

            return $this->redirectToRoute('app_apiary_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('apiary/edit.html.twig', [
            'apiary' => $apiary,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_apiary_delete', methods: ['POST'])]
    public function delete(Request $request, Apiary $apiary, ApiaryRepository $apiaryRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $apiary->getId(), $request->request->get('_token'))) {
            $apiaryRepository->remove($apiary, true);
        }

        return $this->redirectToRoute('app_apiary_index', [], Response::HTTP_SEE_OTHER);
    }
}
