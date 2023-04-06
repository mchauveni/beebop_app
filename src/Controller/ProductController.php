<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Beekeeper;
use App\Form\ProductType;
use App\Repository\BeehiveRepository;
use App\Repository\BeekeeperRepository;
use App\Repository\ProductRepository;
use App\Repository\ApiaryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/product')]
class ProductController extends AbstractController
{
    #[Route('/{id}', name: 'app_product_index', methods: ['GET', 'POST'])]
    public function index(ProductRepository $productRepository, BeekeeperRepository $beekeeperRepository, ApiaryRepository $apiaryRepository, int $id): Response
    {
        $this->denyAccessUnlessGranted('ROLE_BEEKEEPER');
        $beekeeper = $beekeeperRepository->find($id);
        $apiaries = $apiaryRepository->findApiariesByBeekeeper($id);
        $beehive = $beehiveRepository->find($id);

        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findProductsByBeehiveId($id),
            'apiaries' => $apiaries,
            'beehive' => $beehive
        ]);
    }

    #[Route('/{id}/show', name: 'app_product_by_apiary_index', methods: ['GET', 'POST'])]
    public function show(ProductRepository $productRepository, BeehiveRepository $beehiveRepository, ApiaryRepository $apiaryRepository, int $id): Response
    {
        $apiary = $apiaryRepository->find($id);
        $beehives = $beehiveRepository->findBeehivesByApiary($id);
        $beekeeper = $apiary->getBeekeeper()->getId();

        return $this->render('product/show.html.twig', [
            'products' => $productRepository->findProductsByBeehiveId($id),
            'beehives' => $beehives,
            'apiary' => $apiary,
            'beekeeper' => $beekeeper
        ]);
    }

    #[Route('/{id}/showBeehive', name: 'app_product_by_beehive_show', methods: ['GET', 'POST'])]
    public function showByBeehive(ProductRepository $productRepository, BeehiveRepository $beehiveRepository, ApiaryRepository $apiaryRepository, int $id): Response
    {
        $beehive = $beehiveRepository->find($id);
        $products = $productRepository->findProductsByBeehiveId($id);
        return $this->render('product/showBeehive.html.twig', [
            'beehive' => $beehive,
            'products' => $products,
            'apiary'=>$beehive->getApiary()->getId()
        ]);
    }


    #[Route('/{id}/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        int $id,
        BeehiveRepository $beehiveRepository,
        ProductRepository $productRepository,
        EntityManagerInterface $em
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_BEEKEEPER');
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        $beehive = $beehiveRepository->find($id);

        if ($form->isSubmitted() && $form->isValid()) {
            $product
                ->setBeehive($beehiveRepository->find($id));
            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('app_product_by_beehive_show', ['id' => $id], Response::HTTP_SEE_OTHER);
        }
        $products = $productRepository->findProductsByBeehiveId($id);
        return $this->renderForm('product/new.html.twig', [
            'products' => $products,
            'form' => $form,
            'idBeehive' => $id
        ]);
    }

    #[Route('/{id}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, ProductRepository $productRepository, 
    int $id, BeehiveRepository $beehiveRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_BEEKEEPER');

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        $beehive = $beehiveRepository->find($id);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $productRepository->save($product, true);
            return $this->redirectToRoute('app_product_by_beehive_show', ['id' => $product->getBeehive()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
            'idBeehive' => $id
        ]);
    }

    #[Route('/{id}/delete', name: 'app_product_delete', methods: ['POST', 'GET'])]
    public function delete(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_BEEKEEPER');

        $idBeehive = $product->getBeehive()->getId();
        $productRepository->remove($product, true);
        return $this->redirectToRoute('app_product_by_beehive_show', ['id'=>$idBeehive], Response::HTTP_SEE_OTHER);
    }
}
