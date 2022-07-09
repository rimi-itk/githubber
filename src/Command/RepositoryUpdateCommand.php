<?php

namespace App\Command;

use App\Entity\Organization;
use App\Repository\OrganizationRepository;
use App\RepositoryHelper;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:repository:update',
    description: 'Add a short description for your command',
)]
class RepositoryUpdateCommand extends Command
{
    public function __construct(private OrganizationRepository $organizationRepository, private RepositoryHelper $helper)
    {
        parent::__construct(null);
    }

    protected function configure(): void
    {
        $this
            ->addOption('organization', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Organization name')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $organizationNames = $input->getOption('organization');
        /** @var Organization[] $organizations */
        $organizations = empty($organizationNames)
            ? $this->organizationRepository->findAll()
            : $this->organizationRepository->findBy(['name' => $organizationNames]);

        $this->helper->setLogger(new ConsoleLogger($output));
        foreach ($organizations as $organization) {
            $io->section($organization->getName());
            $this->helper->updateRepositories($organization);
        }

        return Command::SUCCESS;
    }
}
