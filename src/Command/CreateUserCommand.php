<?php

namespace App\Command;

use App\Domain\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(name: 'app:create-user')]
class CreateUserCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED)
            ->addArgument('password', InputArgument::REQUIRED)
            ->addArgument('name', InputArgument::REQUIRED)
            ->addArgument('role', InputArgument::REQUIRED)
            ->addArgument('manager_id', InputArgument::OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $user = new User();
        $user->setEmail($input->getArgument('email'));

        $hashedPassword = $this->passwordHasher->hashPassword($user, $input->getArgument('password'));
        $user->setPassword($hashedPassword);

        $user->setName($input->getArgument('name'));
        $user->setRole($input->getArgument('role'));

        if ($input->getArgument('manager_id')) {
            $manager = $this->em->getRepository(User::class)->find($input->getArgument('manager_id'));
            if (!$manager) {
                $output->writeln('<error>Manager not found.</error>');
                return Command::FAILURE;
            }
            $user->setManager($manager);
        } else {
            $user->setManager(null);
        }

        $this->em->persist($user);
        $this->em->flush();

        $output->writeln('<info>User created successfully.</info>');

        return Command::SUCCESS;
    }
}
