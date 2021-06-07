<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * @Route("/product")
 */
class ProductController extends AbstractController
{
    /**
     * @Route("/", name="product_index", methods={"GET"})
     */
    public function index(ProductRepository $productRepository, CategoryRepository $CategoryRepository): Response
    {   
        $products = $productRepository->findLimit(6);


            
        return $this->render('index.html.twig', [
            'products' => $products,
        ]);
    }

    /**
     * @Route("/all", name="product_indexAll", methods={"GET"})
     */
    public function indexAll(ProductRepository $productRepository, CategoryRepository $CategoryRepository): Response
    {   
        $products = $productRepository->findAll();
        $form = $this->createFormBuilder()
            ->add('product',TextType::class)
            ->add('search',SubmitType::class)
            ->getForm();
        
        if ($form->isSubmitted()) {

            dd($form);
        }
        return $this->render('brand.html.twig', [
            'products' => $products,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/new", name="product_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $file = $request->files->get('product')['image'];

            $uploads_directory = $this->getParameter('uploads_directory');

            $filename  = md5(uniqid()).'.'.$file->guessExtension();
            $file->move(
                $uploads_directory,
                $filename
            );
            $product->setImage($filename);

            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('product_index');
        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="product_show", methods={"GET"})
     */
    public function show(Product $product): Response
    {
        
        return $this->render('show.html.twig', [
            'product' => $product,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="product_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Product $product): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('product_index');
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="product_delete", methods={"POST"})
     */
    public function delete(Request $request, Product $product): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($product);
            $entityManager->flush();
        }
        return $this->redirectToRoute('product_index');
    }
    /**
     * @Route("/search", name="find_product", methods={"POST"})
     */
    public function find(Request $request): ?Response{
        dd($request);
        return $this->redirectToRoute('product_index');
    }
}
