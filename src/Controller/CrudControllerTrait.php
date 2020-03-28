<?php
declare(strict_types=1);

namespace App\Controller;

use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

trait CrudControllerTrait
{
    /**
     * @return ObjectManager|EntityManager
     */
    protected function getEntityManager(): ObjectManager
    {
        return $this->getDoctrine()->getManager();
    }

    protected function createDeleteForm($object): FormInterface
    {
        return $this->createFormBuilder($object)
            ->setMethod('DELETE')
            ->getForm()
            ;
    }

    /**
     * @param FormInterface $form
     * @param Request $request
     * @param string $successMessage #TranslationKey
     * @return bool
     */
    protected function handleForm(FormInterface $form, Request $request, string $successMessage = null): bool
    {
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->saveObject($form->getData());
            $this->addFlash('success', $successMessage ?: 'save_successfully');
            return true;
        }
        return false;
    }

    /**
     * @param FormInterface $form
     * @param Request $request
     * @param string $successMessage #TranslationKey
     * @param string $failedMessage #TranslationKey
     * @return bool
     */
    protected function handleDeleteForm(FormInterface $form, Request $request, string $successMessage = null, string $failedMessage = null): bool
    {
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $object = $form->getData();
            if ($this->isDeletable($object)) {
                $this->deleteObject($object);
                $this->addFlash('success', $successMessage ?: 'delete_successfully');
                return true;
            } else {
                $this->addFlash('error', $failedMessage ?: 'delete_failed');
            }
        }
        return false;
    }

    protected function saveObject($object): void
    {
        $em = $this->getEntityManager();
        $em->persist($object);
        $em->flush();
    }

    protected function deleteObject($object): void
    {
        $em = $this->getEntityManager();
        $em->remove($object);
        $em->flush();
    }

    /**
     * Tries to delete an object without actually deleting it.
     * Returns false if ForeignKeyConstraintViolationException would be thrown; true otherwise.
     */
    protected function isDeletable($object): bool
    {
        try {
            $em = $this->getEntityManager();

            $em->beginTransaction();

            // Use query instead of $em->remove()
            // With $em->remove, the object's id will become null, which will make it hard to manage afterwards.
            $em->createQueryBuilder()
                ->delete(get_class($object), 'o')
                ->where('o = :object')
                ->setParameter('object', $object)
                ->getQuery()
                ->execute()
            ;

            $em->rollback();
            return true;
        } catch (ForeignKeyConstraintViolationException $e) {
            return false;
        }
    }
}