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
        $product = $productRepository->findBy(['enabled'=>true]);
        return $this->render('pili_pili/index.html.twig', [
            'controller_name' => 'PiliPiliController',
            'data'=>$product
        ]);
    }

    /**
     * @Route("/products", name="index_product")
     */
    public function products(ProductRepository $productRepository):Response
    {
        $products = $productRepository->findAll();

        return $this->render('pili_pili/products.html.twig',[
            'data' => $products
        ]);
    }

    /**
     * @Route("/product/create", name="create_product")
     */
    public function createProduct(Request $request, BrandRepository $brandRepository, EntityManagerInterface $em, ProductRepository $productRepository):Response
    {
        $product = new Product();
        $brand = $brandRepository->findAll();
        $brandName = array();
        foreach ($brand as $key => $value) {
            array_push($brandName, $value->getName());
        }

        $form = $this->createFormBuilder($product)
                ->add('name',TextType::class,[
                    'label'=>"Name",
                    'attr'=>[
                        'class'=>'form-control'
                        ]
                ])
                ->add('description',TextareaType::class,[
                    'label'=>'Description',
                    'attr'=>[
                        'class'=>'form-control'
                        ]
                ])
                ->add('brand_id',ChoiceType::class,[
                    'choices'=>$brand,
                    'choice_label'=> function (?Brand $bra){
                        return $bra ? $bra->getName() : '';
                    },
                    'label'=>'Brand',
                    'attr'=>[
                        'class'=>'form-control'
                        ]
                ])
                ->add('price',NumberType::class,[
                    'label'=>'Price',
                    'attr'=>[
                        'class'=>'form-control'
                    ]
                ])
                ->add('save',SubmitType::class,[
                    'label'=>'Save',
                    'attr'=>[
                        'class'=>'btn btn-primary'
                        ]
                ])
                ->getForm();

                $form->handleRequest($request);
                if($form->isSubmitted() && $form->isValid())
                {
                    $product = $form->getData();

                $id = $product->getId();
                $product->setCreatedAt(new DateTime());
                $product->setEnabled(false);
                $product->setSlug(rand(2,100));
                
                $em->persist($product);
                $em->flush();

                return $this->redirectToRoute("index_product",[
                    'data'=>$productRepository->findAll()
                ]);
                }
       
        return $this->render('pili_pili/product-create.html.twig',[
            'form'=>$form->createView()
        ]);
    }
}
