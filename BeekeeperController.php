<?php

namespace App\Controller;

use App\Entity\Beekeeper;
use App\Repository\BeekeeperRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

// petite astuce : on place une écriture générique de route en tête de classe
// avec un nom générique et une méthode par défaut
#[Route('/beekeeper', name: 'app_beekeeper_', methods: ['GET'])]
class BeekeeperController extends AbstractController
{
    // lister les apiculteurs
    #[Route('/', name: 'index')]
    public function index(
        BeekeeperRepository $beekeeperRepository
    ): Response {
        // récupérer tous les apiculteurs
        $beekeepers = $beekeeperRepository->findBy([], ['id' => 'DESC']);
        return $this->render('beekeeper/index.html.twig', [
            'beekeepers' => $beekeepers,
        ]);
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
}
