<?php

namespace Authentication\Presentation\Command;

use Authentication\Domain\Repository\AccessCredentialRepositoryInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:cleanup:expired-credentials',
    description: 'Remove expired magic links and OTP codes'
)]
final class CleanupExpiredCredentialsCommand extends Command
{
    public function __construct(
        private readonly AccessCredentialRepositoryInterface $repository
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $count = $this->repository->removeExpired();

        $output->writeln(sprintf('Removed %d expired credentials', $count));

        return Command::SUCCESS;
    }
}
