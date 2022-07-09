<?php

namespace App;

use App\Entity\Organization;
use App\Entity\Repository;
use App\Repository\RepositoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerTrait;
use Psr\Log\NullLogger;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Process\Process;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class RepositoryHelper
{
    use LoggerAwareTrait;
    use LoggerTrait;

    private array $option;

    public function __construct(private HttpClientInterface $httpClient, private RepositoryRepository $repositoryRepository, private EntityManagerInterface $entityManager, private Filesystem $filesystem, $options)
    {
        $this->logger = new NullLogger();

        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $this->options = $resolver->resolve($options);
    }

    public function updateRepositories(Organization $organization)
    {
        // Soft delete all existing repositories.
        $this->entityManager->getFilters()->disable('softdeleteable');
        /** @var Repository[] $repositories */
        $repositories = $this->repositoryRepository->findBy(['organization' => $organization]);
        foreach ($repositories as $repository) {
            $repository->setDeletedAt(new \DateTime());
        }
        $this->entityManager->flush();
        $this->entityManager->getFilters()->enable('softdeleteable');

        $url = sprintf('https://api.github.com/%s/%s/repos', $organization->isUser() ? 'users' : 'orgs', $organization->getName());

        while (null !== $url) {
            $this->debug($url);
            $response = $this->httpClient->request('GET', $url, [
                'query' => [
                    'per_page' => 100,
                ],
            ]);

            $this->update($organization, $response->toArray());

            $url = null;
            $headers = $response->getHeaders();
            if (isset($headers['link'])) {
                $link = reset($headers['link']);

                if (false !== $link && preg_match_all('/<(?P<url>[^>]+)>;\s*rel="(?P<rel>[^"]+)"/', $link, $matches, PREG_SET_ORDER)) {
                    foreach ($matches as $match) {
                        if ('next' === $match['rel']) {
                            $url = $match['url'];
                            break;
                        }
                    }
                }
            }
        }
    }

    public function clone(Repository $repository)
    {
        if ($repository->isDeleted()) {
            return;
        }

        $organizationDir = $this->options['clone_base_dir'].'/'.$repository->getOrganization()->getName();
        if (!$this->filesystem->exists($organizationDir)) {
            $this->filesystem->mkdir($organizationDir);
        }

        $repositoryDir = $organizationDir.'/'.$repository->getName();
        $this->logger->debug($repositoryDir);
        if ($this->filesystem->exists($repositoryDir)) {
            $command = [
                'git',
                '-C', $repositoryDir,
                'pull',
            ];
        } else {
            $command = [
                'git',
                'clone',
                $repository->getData()['clone_url'],
                $repositoryDir,
            ];
        }

        $process = new Process($command);
        $process->mustRun(function ($type, $buffer) {
            if (Process::ERR === $type) {
                $this->logger->error($buffer);
            } else {
                $this->logger->info($buffer);
            }
        });
    }

    public function log($level, \Stringable|string $message, array $context = []): void
    {
        $this->logger->log($level, $message, $context);
    }

    private function update(Organization $organization, array $data)
    {
        foreach ($data as $item) {
            $name = $item['name'];
            $repository = $this->repositoryRepository->findOneBy([
                'name' => $name,
                'organization' => $organization,
            ]);
            if (null === $repository) {
                $repository = (new Repository())
                    ->setName($name)
                    ->setOrganization($organization);
            }

            $repository
                ->setData($item)
                ->setDeletedAt();
            $this->entityManager->persist($repository);
            $this->entityManager->flush();
        }
    }

    private function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired([
                'clone_base_dir',
            ]);
    }
}
