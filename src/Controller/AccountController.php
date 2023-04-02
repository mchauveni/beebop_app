<?php

namespace App\Controller;

use App\Entity\Beekeeper;
use App\Form\BeekeeperType;
use App\Repository\BeekeeperRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AccountController extends AbstractController
{
    private $passwordHasher;
    // injecter le service de cryptage
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    #[Route('/register', name: 'app_beekeeper_register', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        BeekeeperRepository $beekeeperRepository,
        EntityManagerInterface $em
    ): Response {
        $beekeeper = new Beekeeper();
        $form = $this->createForm(BeekeeperType::class, $beekeeper);
        $form->handleRequest($request);

        // Si le formulaire a été soumis et que les données sont valides

        if ($form->isSubmitted() && $form->isValid()) {

            // récupérer les données soumises par le formulaire
            $beekeeper = $form->getData();
            $beekeeper
                ->setRoles(['ROLE_BEEKEEPER'])
                ->setVerified(true)
                ->setPassword($this->passwordHasher->hashPassword($beekeeper, $form->get('password')->getData()));

            // enregistrer les données dans la base
            $em->persist($beekeeper);
            $em->flush();
            // rediriger vers l’accueil
            return $this->redirectToRoute('app_login');
        }
        // sinon afficher le formulaire
        return $this->render('account/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
