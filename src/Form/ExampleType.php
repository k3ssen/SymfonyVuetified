<?php

namespace App\Form;

use App\Vue\VueForm;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\LessThan;
use Symfony\Component\Validator\Constraints\NotEqualTo;

/**
 * An example form to demonstrate how the form can be used with Vue.
 * You should remove this form (after watching the example first ;p ).
 */
class ExampleType extends AbstractType implements \JsonSerializable
{
    /**
     * @var FormFactory
     */
    private $factory;
    /**
     * @var RequestStack
     */
    private RequestStack $requestStack;

    public function __construct(FormFactoryInterface $factory, RequestStack $requestStack)
    {
        $this->factory = $factory;
        $this->requestStack = $requestStack;
    }

    public function jsonSerialize()
    {
        $builder = $this->factory->createBuilder();
        $this->buildForm($builder, []);
        $form = $builder->getForm();
        $form->handleRequest($this->requestStack->getMasterRequest());
        return VueForm::create($form);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new Length(['value' => 5, 'exactMessage' => 'The name must contain 5 characters']),
                ],
                'label' => 'Name',
                'attr' => [
                    'counter' => '25',
                ]
            ])
            ->add('amount', NumberType::class, [
                'label' => 'Amount',
                'attr' => [
                    'prepend-inner-icon' => 'mdi-currency-eur',
                ],
            ])
            ->add('dateField', DateType::class, [
                'label' => 'Start date',
            ])
            ->add('timeField', TimeType::class, [
                'label' => 'Start time',
            ])
            ->add('datetime', DateTimeType::class, [
                'label' => 'Date & time',
                'minutes' => [0, 15, 30, 45],
                'date_label' => 'Date',
                'time_label' => 'Time',
            ])
            ->add('rangeType', RangeType::class, [
                'label' => 'Range',
                'attr' => [
                    'min' => 5,
                    'max' => 50,
                    'thumb-size' => 20,
                    'thumb-label' => 'always'
                ],
                'constraints' => [
                    new LessThan('30'),
                ],
            ])
            ->add('checkbox', CheckboxType::class, [
                'label' => 'checkbox',
                'help' => 'When you check me, the amount-field will be disabled',
            ])
            ->add('toggle', CheckboxType::class, [
                'label' => 'toggle',
                'block_prefix' => 'SwitchType',
                'constraints' => [
                    new NotEqualTo(true),
                ],
            ])
            ->add('radioChoice', ChoiceType::class, [
                'label' => 'Select single option',
                'expanded' => true,
                'attr' => [
                    'row' => true,
                ],
                'choice_attr' => function() {
                    return [
                        'hint' => 'Some hint!',
                        'persistent-hint' => true,
                    ];
                },
                'choices' => array_combine($values = [
                    'A', 'B', 'C'
                ], $values),
                'constraints' => [
                    new NotEqualTo('B'),
                ],
            ])
            ->add('checkboxChoice', ChoiceType::class, [
                'label' => 'Select multi option',
                'expanded' => true,
                'multiple' => true,
                'choice_attr' => function() {
                    return [
                        'hint' => 'Some hint!',
                        'persistent-hint' => true,
                        'style' => 'margin-top: 0px;'
                    ];
                },
                'attr' => [
                    'row' => true,
                ],
                'choices' => array_combine($values = [
                    'A', 'B', 'C'
                ], $values),
            ])
            ->add('select', ChoiceType::class, [
                'label' => 'Select single option',
                'choices' => array_combine($values = [
                    'A', 'B', 'C'
                ], $values),
            ])
            ->add('selectAllowAdd', ChoiceType::class, [
                'label' => 'Select option or add custom value',
                'required' => false,
                'choices' => array_combine($values = [
                    'A', 'B', 'C'
                ], $values),
                'allow_add' => true,
            ])
            ->add('multiple', ChoiceType::class, [
                'label' => 'Select multiple options and/or add custom values',
                'multiple' => true,
                'choices' => array_combine($values = [
                    'A', 'B', 'C'
                ], $values),
                'allow_add' => true,
            ])

            ->add('collection', CollectionType::class, [
                'label' => 'Descriptions',
                'allow_add' => true,
                'allow_delete' => true,
                'entry_type' => TextType::class,
                'entry_options' => [
                    'label' => 'Text'
                ],
                'constraints' => [
                    new Count(1),
                ],
                'error_bubbling' => false,
            ])
        ;
    }
}
