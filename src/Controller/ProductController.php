<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\BeehiveRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/product')]
class ProductController extends AbstractController
{
    #[Route('/{id}', name: 'app_product_index', methods: ['GET', 'POST'])]
    public function index(ProductRepository $productRepository, BeehiveRepository $beehiveRepository,int $id): Response
    {
        $beehive = $beehiveRepository->find($id);
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findProductsByBeehiveId($id),
            'beehive' => $beehive
        ]);
    }

    #[Route('/{id}/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        int $id,
        BeehiveRepository $beehiveRepository,
        EntityManagerInterface $em
    ): Response {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $product
                ->setBeehive($beehiveRepository->find($id));
            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('app_product_index', ['id' => $id], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('product/new.html.twig', [
            'product' => $product,
            'form' => $form,
            'idBeehive' => $id
        ]);
    }

    #[Route('/{id}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, ProductRepository $productRepository, 
    int $id, BeehiveRepository $beehiveRepository): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        $idBeehive = $product->getBeehive()->getId();
        if ($form->isSubmitted() && $form->isValid()) {
            $productRepository->save($product, true);
            return $this->redirectToRoute('app_product_index', ['id' => $idBeehive], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
            'idBeehive' => $idBeehive
        ]);
    }

    #[Route('/{id}/delete', name: 'app_product_delete', methods: ['POST', 'GET'])]
    public function delete(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        $idBeehive = $product->getBeehive()->getId();
        $productRepository->remove($product, true);
        return $this->redirectToRoute('app_product_index', ['id'=>$idBeehive], Response::HTTP_SEE_OTHER);
    }
}
