<?php

namespace App\Command;

use App\Services\Puzzle\Infrastructure\GameInvitationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:clear-expired-invites',
    description: 'Deletes expired game invitations from the database',
)]
class ClearExpiredInvitesCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private GameInvitationRepository $gameInvitationRepository
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $expiredInvitations = $this->gameInvitationRepository->getExpiredInvitations();

        $count = count($expiredInvitations);
        if ($count === 0) {
            $io->info('There are no expired invitations to clear');
        }

        foreach ($expiredInvitations as $expiredInvitation) {

            $io->info(sprintf('Removing invitation with code %s', $expiredInvitation->getInvitationCode()));
            $this->entityManager->remove($expiredInvitation);
        }
        $this->entityManager->flush();



        $io->success(sprintf('%s expired Invitations cleared successfully.', $count));

        return Command::SUCCESS;
    }
}
