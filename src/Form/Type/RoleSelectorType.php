<?php
declare(strict_types=1);

namespace App\Form\Type;

use App\Security\Roles;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoleSelectorType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $roleChoices = array_combine(Roles::getRoleChoices(), Roles::getRoleChoices());

        $resolver->setDefaults([
            'choices' => $roleChoices,
            'multiple' => true,
            'choice_translation_domain' => 'roles',
            'attr' => [
                'data-role' => 'select2',
            ],
        ]);
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}
