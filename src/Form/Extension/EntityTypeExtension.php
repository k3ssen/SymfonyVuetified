<?php
declare(strict_types=1);

namespace App\Form\Extension;

use Doctrine\Common\Inflector\Inflector;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

class EntityTypeExtension extends AbstractTypeExtension
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;
    /**
     * @var RouterInterface
     */
    protected $router;

    public function __construct(EntityManagerInterface $em, RouterInterface $router)
    {
        $this->em = $em;
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefault('tags', false);
        $resolver->setAllowedTypes('tags', ['boolean', 'callable']);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['tags']) {
            $this->addTagsFieldListener($builder, is_callable($options['tags']) ? $options['tags'] : null);
        }
        parent::buildForm($builder, $options);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $attr = $view->vars['attr'] ?? [];

        if ($options['tags']) {
            $attr['tags'] = $attr['tags'] ?? true;
        }

        $view->vars['attr'] = $attr;
    }

    protected function addTagsFieldListener(FormBuilderInterface $builder, ?callable $createCallback = null)
    {
        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use ($createCallback) {
            $form = $event->getForm();
            // We only need to add a new entry if getData returns null (which means no existing entry was found)
            if ($form->getData() !== null) {
                return;
            }
            $parentForm = $form->getParent();
            $formName = $form->getName();
            if (!$parentForm) {
                throw new \RuntimeException(sprintf('Cannot use tags for form or field "%s": a parent form is required', $formName));
            }

            // Create a new entry based on the provided className.
            $class = $form->getConfig()->getOption('class');
            $newEntry = new $class();
            // Fetch the parentEntry and have this new entry set on that parentEntry
            $parentEntry = $parentForm->getData();
            $setter = 'set'.Inflector::camelize($formName);
            if ($parentEntry && method_exists($parentEntry, $setter)) {
                $parentEntry->$setter($newEntry, $event->getData());
            }
            if (is_callable($createCallback)) {
                $createCallback($newEntry, $event);
            }

            $this->em->persist($newEntry);
            $this->em->flush();
            // (re)add this childform to the parent form, so that the newly add option is added and considered valid.
            $parentForm->add($form->getName(), null, $form->getConfig()->getOptions());
        });
    }

    public static function getExtendedTypes(): array
    {
        return [EntityType::class];
    }
}
