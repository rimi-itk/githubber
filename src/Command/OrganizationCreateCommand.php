<?php

namespace App\Command;

use App\Entity\Organization;
use App\Repository\OrganizationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:organization:create',
    description: 'Add a short description for your command',
)]
class OrganizationCreateCommand extends Command
{
    public function __construct(private OrganizationRepository $organizationRepository, private EntityManagerInterface $entityManager)
    {
        parent::__construct(null);
    }

    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED, 'Organization name')
            ->addOption('is-user', null, InputOption::VALUE_NONE, 'Is user?')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $name = $input->getArgument('name');
        $isUser = $input->getOption('is-user');

        /** @var Organization $organization */
        $organization = $this->organizationRepository->findOneByName($name);
        if (null !== $organization) {
            $io->warning(sprintf('Organization %s already exists', $organization->getName()));

            return Command::SUCCESS;
        }
        $organization = (new Organization())
            ->setName($name)
            ->setIsUser($isUser);
        $this->entityManager->persist($organization);
        $this->entityManager->flush();

        $io->success(sprintf('Organization %s created', $organization->getName()));

        return Command::SUCCESS;
    }
}
