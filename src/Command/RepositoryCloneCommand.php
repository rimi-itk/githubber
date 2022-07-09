<?php

namespace App\Command;

use App\Repository\RepositoryRepository;
use App\RepositoryHelper;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:repository:clone',
    description: 'Add a short description for your command',
)]
class RepositoryCloneCommand extends Command
{
    public function __construct(private RepositoryRepository $repositoryRepository, private RepositoryHelper $helper)
    {
        parent::__construct(null);
    }

    protected function configure(): void
    {
        $this
            ->addOption('repository', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Repository')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $repositories = $this->repositoryRepository->findAll();

        $this->helper->setLogger(new ConsoleLogger($output));
        foreach ($repositories as $repository) {
            $io->section(sprintf('%s/%s', $repository->getOrganization()->getName(), $repository->getName()));
            $this->helper->clone($repository);
        }

        return Command::SUCCESS;
    }
}
