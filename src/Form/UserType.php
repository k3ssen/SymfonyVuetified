<?php
declare(strict_types=1);

namespace App\Form;

use App\Entity\User;
use App\Form\Type\RoleSelectorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserType extends AbstractType
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var User $user */
        $user = $builder->getData();

        $builder
            ->add('username', null, [
                'label' => 'Username',
            ])
            ->add('email', null, [
                'label' => 'Email',
            ])
            ->add('roles', RoleSelectorType::class, [
                'required' => false,
                'label' => 'Roles',
                'help' => 'Note that all users will automatically have the \'user\' role.',
            ])
            ->add('new_password', PasswordType::class, [
                'required' => false,
                'label' => 'Password',
                'help' => $user->getId() ? 'Leave blank to keep same password' : '',
                'mapped' => false,
            ])
        ;

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            /** @var User $user */
            $user = $event->getData();
            $password = $event->getForm()->get('new_password')->getData();
            if ($password) {
                $user->setPassword($this->passwordEncoder->encodePassword($user, $password));
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}