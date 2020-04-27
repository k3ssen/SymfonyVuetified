<?php
declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    /**
     * @Route("/", name="dashboard")
     */
    public function index()
    {
        return $this->render('dashboard/index.vue.twig', [
            'controller_name' => 'DashboardController',
        ]);
    }

    /**
     * @Route("/example2", name="example2")
     */
    public function example2()
    {
        return $this->render('dashboard/example2.vue.twig', [
            'controller_name' => 'DashboardController',
        ]);
    }

    /**
     * @Route("/ajaxTest.js", name="ajaxTest")
     */
    public function ajaxTest()
    {
        return $this->render('dashboard/ajaxTest.html.twig', [
            'controller_name' => 'DashboardController',
        ]);
//        $response->headers->set('Content-Type','text/javascript');
//        return $response;
    }
}
