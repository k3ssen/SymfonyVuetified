<?php
declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use App\Security\Roles;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;
    /**
     * @var ObjectManager
     */
    protected $manager;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;
        $this->createAdmin();
        $this->manager->flush();
    }

    protected function createAdmin(): void
    {
        $user = (new User())
            ->setUsername('admin')
            ->setEmail('admin@app.test')
            ->setRoles([Roles::ROLE_SUPER_ADMIN])
        ;
        $user->setPassword($this->passwordEncoder->encodePassword($user, 'admin'));
        $this->manager->persist($user);
    }
}
