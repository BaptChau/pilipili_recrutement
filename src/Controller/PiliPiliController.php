<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PiliPiliController extends AbstractController
{
    /**
     * @Route("/pili/pili", name="pili_pili")
     */
    public function index(): Response
    {
        return $this->render('pili_pili/index.html.twig', [
            'controller_name' => 'PiliPiliController',
        ]);
    }
}
