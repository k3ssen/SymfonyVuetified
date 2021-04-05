<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ExampleController extends AbstractController
{
    /**
     * @Route("/", name="example")
     */
    public function index(): Response
    {
        return $this->render('example/index.html.twig', [
            'controller_name' => 'DashboardController',
        ]);
    }

    /**
     * @Route("/example-fetch", name="example_fetch")
     */
    public function fetchExample(): Response
    {
        return $this->render('example/fetch.html.twig', [
            'controller_name' => 'DashboardController',
        ]);
    }

    /**
     * @Route("/example-form", name="example_form")
     */
    public function formExample(): Response
    {
        return $this->render('example/form.html.twig', [
            'controller_name' => 'DashboardController',
        ]);
    }
}
