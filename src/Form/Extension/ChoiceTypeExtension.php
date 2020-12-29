<?php
declare(strict_types=1);

namespace App\Form\Extension;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class ChoiceTypeExtension extends AbstractTypeExtension
{
    public static function getExtendedTypes(): array
    {
        return [ChoiceType::class];
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefault('allow_add', false);;
        $resolver->setAllowedTypes('allow_add', ['boolean', 'callable']);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['allow_add']) {
            if (($options['class'] ?? null) && !is_callable($options['allow_add'])) {
                throw new InvalidOptionsException('When used for entities, The "allow_add" option must be a callable to create the new entity.');
            }
            $this->addTagsFieldListener($builder);
        }
        parent::buildForm($builder, $options);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $attr = $view->vars['attr'] ?? [];
        if ($options['allow_add']) {
            $attr['allow_add'] = $attr['allow_add'] ?? true;
        }
        $view->vars['attr'] = $attr;
    }

    protected function addTagsFieldListener(FormBuilderInterface $builder)
    {
        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();

            // If data is not null, then no new value(s) is/are added.
            if ($form->getData() !== null) {
                return;
            }

            $parentForm = $form->getParent();
            $formName = $form->getName();
            if (!$parentForm) {
                throw new \RuntimeException(sprintf(
                    'Cannot use %s option for form or field "%s": a parent form is required',
                    'allow_add',
                    $formName
                ));
            }

            $submittedData = $event->getData();

            $options = $form->getConfig()->getOptions();

            $parentEntry = $parentForm->getData();
            $accessor = new PropertyAccessor();
            if ($options['multiple'] ?? false) {
                foreach ($submittedData as $key => $submittedValue) {
                    $value = $this->handleInputValue($submittedValue, $options);
                    $submittedData[$key] = $value;
                }
                if ($accessor->isWritable($parentEntry, $formName)) {
                    $accessor->setValue($parentEntry, $formName, $submittedData);
                }
            } else {
                $value = $this->handleInputValue($submittedData, $options);
                if ($accessor->isWritable($parentEntry, $formName)) {
                    $accessor->setValue($parentEntry, $formName, $value);
                }
            }

            // (re)add this childForm to the parent form, so that the newly add option is added and considered valid.
            $parentForm->add($formName, get_class($form->getConfig()->getType()->getInnerType()), $form->getConfig()->getOptions());
        });
    }

    protected function handleInputValue($inputValue, array &$options) {
        $em = $options['em'] ?? null;
        $class = $options['class'] ?? null;
        $value = $inputValue;
        // If we're dealing with entities, then find use the value to find the matching entity.
        if ($em instanceof EntityManager && $class) {
            $value = $em->find($class, $inputValue);
        }
        if (!is_object($value) && !in_array($inputValue, $options['choices'] ?? [])) {
            if (is_callable($options['allow_add'])) {
                $value = $options['allow_add']($inputValue);
                if ($em instanceof EntityManager) {
                    $em->persist($value);
                }
            }
            $options['choices'][$inputValue] = $value;
        }
        return $value;
    }
}