<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\CrudControllerTrait;
use App\Entity\User;
use App\Form\JsonForm;
use App\Form\UserType;
use App\Security\UserVoter;
use App\Datatable\UserDatatable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/admin/user")
 */
class UserController extends AbstractController
{
    use CrudControllerTrait;

    /**
     * @Route("/", name="admin_user_index")
     */
    public function index(UserDatatable $datatable): Response
    {
        $this->denyAccessUnlessGranted(UserVoter::INDEX);

//        dump(json_encode($datatable)); exit();

        return $this->render('admin/user/index.vue.twig', [
            'datatable' => $datatable,
        ]);
    }

    /**
     * @Route("/result", name="admin_user_result")
     */
    public function result(UserDatatable $datatable): Response
    {
        $this->denyAccessUnlessGranted(UserVoter::SEARCH);

        return JsonResponse::create($datatable);
    }

    /**
     * @Route("/{id}", name="admin_user_show", requirements={"id":"\d+"})
     */
    public function show(User $user): Response
    {
        $this->denyAccessUnlessGranted(UserVoter::VIEW, $user);

        return $this->render('admin/user/show.vue.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/new", name="admin_user_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $user = new User();
        $this->denyAccessUnlessGranted(UserVoter::CREATE, $user);

        $form = $this->createForm(UserType::class, $user);
        if ($this->handleForm($form, $request)) {
            return $this->redirectToRoute('admin_user_index');
        }
        return $this->render('admin/user/new.vue.twig', [
            'user' => $user,
            'jsonForm' => JsonForm::create($form),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="admin_user_edit",  methods="GET|POST")
     */
    public function edit(Request $request, User $user): Response
    {
        $this->denyAccessUnlessGranted(UserVoter::EDIT, $user);

        $form = $this->createForm(UserType::class, $user);
        if ($this->handleForm($form, $request)) {
            return $this->redirectToRoute('admin_user_index');
        }

        return $this->render('admin/user/edit.vue.twig', [
            'user' => $user,
            'jsonForm' => JsonForm::create($form),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="admin_user_delete",  methods="GET|DELETE")
     */
    public function delete(Request $request, User $user): Response
    {
        $this->denyAccessUnlessGranted(UserVoter::DELETE, $user);

        $form = $this->createDeleteForm($user);
        if ($this->handleDeleteForm($form, $request)) {
            return $this->redirectToRoute('admin_user_index');
        }
        return $this->render('admin/user/delete.vue.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
}