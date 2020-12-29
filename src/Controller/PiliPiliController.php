<?php

namespace App\Controller;

use DateTime;
use App\Entity\Brand;
use App\Entity\Product;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManager;
use App\Repository\BrandRepository;
use Symfony\Component\Mime\Message;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PiliPiliController extends AbstractController
{
    /**
     * @Route("/", name="pili_pili")
     */
    public function index(ProductRepository $productRepository): Response
    {
        $product = $productRepository->findBy(['enabled' => true]);
        return $this->render('pili_pili/index.html.twig', [
            'controller_name' => 'PiliPiliController',
            'data' => $product
        ]);
    }

    /**
     * @Route("/products", name="index_product")
     */
    public function products(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();

        return $this->render('pili_pili/products.html.twig', [
            'data' => $products
        ]);
    }

    /**
     * @Route("/product/create", name="create_product")
     */
    public function createProduct(Request $request, BrandRepository $brandRepository, EntityManagerInterface $em, ProductRepository $productRepository): Response
    {
        $product = new Product();
        $slugify = new Slugify();

        $brand = $brandRepository->findAll();


        $form = $this->createFormBuilder($product)
            ->add('name', TextType::class, [
                'label' => "Name",
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('brand_id', ChoiceType::class, [
                'choices' => $brand,
                'choice_label' => function (?Brand $bra) {
                    return $bra ? $bra->getName() : '';
                },
                'label' => 'Brand',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('price', NumberType::class, [
                'label' => 'Price',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Save',
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $product = $form->getData();

            //Géneration et verification du slug
            $rawSlug = $product->getName();
            $slugNumber = count($productRepository->findSlug($slugify->slugify($rawSlug)));
            $slug = $slugify->slugify($rawSlug . $slugNumber);

            $product->setCreatedAt(new DateTime());
            $product->setEnabled(false);
            $product->setSlug($slug);

            $em->persist($product);
            $em->flush();

            $this->addFlash('success', 'Produit Ajouté');
            $this->addFlash('success', 'Pour activer les produits : php bin/console product:enable ');

            return $this->redirectToRoute("index_product");
        }

        return $this->render('pili_pili/product-create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/product/update/{id}", name="update_product")
     *
     * @return Response
     */
    public function updateProduct(int $id, ProductRepository $productRepository, Request $request, BrandRepository $brandRepository, EntityManagerInterface $em): Response
    {
        $brand = $brandRepository->findAll();
        $product = $productRepository->findOneBy(['id' => $id]);
        dump($product);

        $form = $this->createFormBuilder($product)
            ->add('name', TextType::class, [
                'label' => "Name",
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('brand_id', ChoiceType::class, [
                'choices' => $brand,
                'choice_label' => function (?Brand $bra) {
                    return $bra ? $bra->getName() : '';
                },
                'label' => 'Brand',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('price', NumberType::class, [
                'label' => 'Price',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Save',
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ])
            ->getForm();


        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $product = $form->getData();

            $product->setUpdatedAt(new DateTime());
            $product->setEnabled(false);

            $em->persist($product);
            $em->flush();

            $this->addFlash('success', 'Produit Modifié');

            return $this->redirectToRoute("index_product");
        }
        return $this->render('pili_pili/product-update.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
