<?php

namespace App\DataFixtures;

use App\Entity\Agent;
use App\Entity\Operator;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    public const AGENT_REFERENCE = 'AGENT_REFERENCE';
    public const OPERATOR_REFERENCE = 'OPERATOR_REFERENCE';

    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        // create agent user
        $agent = new Agent();
        $agent->setEmail("agent@yopmail.fr");
        $agent->setPassword(
            $this->passwordEncoder->encodePassword($agent, "password")
        );
        $agent->setRoles(["ROLE_AGENT"]);
        $this->addReference(self::AGENT_REFERENCE, $agent);

        $manager->persist($agent);

        // create operator user
        $operator = new Operator();
        $operator->setEmail("operator@yopmail.fr");
        $operator->setRoles(["ROLE_OPERATOR"]);
        $this->addReference(self::OPERATOR_REFERENCE, $operator);

        $manager->persist($operator);

        $manager->flush();
    }
}
