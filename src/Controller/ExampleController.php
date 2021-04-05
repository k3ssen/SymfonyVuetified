<?php

namespace App\Controller;

use App\Form\CustomFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
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
        ]);
    }

    /**
     * @Route("/example-fetch", name="example_fetch")
     */
    public function fetchExample(): Response
    {
        return $this->render('example/fetch.html.twig', [
        ]);
    }

    /**
     * @Route("/example-form", name="example_form")
     */
    public function formExample(): Response
    {
        return $this->render('example/form.html.twig', [
        ]);
    }

    /**
     * @Route("/example-custom-form-types", name="example_custom_form_types")
     */
    public function customFormType(Request $request): Response
    {
        $form = $this->createForm(TextType::class, null, [
            'block_prefix' => 'SvTextEditor',
        ]);
        $form->handleRequest($request);
        return $this->render('example/custom-form-types.html.twig', [
            'form' => $form,
        ]);
    }
}
