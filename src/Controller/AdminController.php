<?php

namespace App\Controller;

use App\Repository\BeekeeperRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin_dashboard')]
    public function index(
        BeekeeperRepository $beekeeperRepository
    ): Response {
        // récupérer tous les apiculteurs
        $beekeepers = $beekeeperRepository->findBy([], ['id' => 'DESC']);
        return $this->render('admin/index.html.twig', [
            'beekeepers' => $beekeepers,
        ]);
    }
}
