<?php

namespace App\DataPersister;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;

class UserDataPersister implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $passwordHasher,
        private ProcessorInterface $decorated
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        if ($data instanceof User && $data->getPassword()) {
            $hashed = $this->passwordHasher->hashPassword($data, $data->getPassword());
            $data->setPassword($hashed);
        }

        return $this->decorated->process($data, $operation, $uriVariables, $context);
    }
}

