<?php

namespace App\Command;

use App\Services\User\Domain\User;
use App\Services\User\Service\UserService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

#[AsCommand(
    name: 'app:user:promote',
    description: 'Promote a user to admin status from the command line.',
)]
class PromoteUserCommand extends Command
{
    public function __construct(private readonly UserService $userService)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'The E-Mail address of the user to be promoted.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');
        try {
            $this->userService->loadUserByIdentifier($email);
        } catch (UserNotFoundException $e) {
            $io->error(sprintf('No user with email "%s" found.', $email));
            return Command::FAILURE;


        } catch (CustomUserMessageAccountStatusException $e) {
            $io->error($e->getMessage());
            return Command::FAILURE;
        }

        $user = $this->userService->loadUserByIdentifier($email);
        if ($user instanceof User) {
            $adminRole = $this->userService->getRoleByHandle('ROLE_ADMIN');

            if ($user->hasRole($adminRole)) {
                $io->warning(sprintf('The user "%s" is already promoted. Aborting', $email));
                return Command::SUCCESS;
            }
            $user->addRole($adminRole);
            $this->userService->saveUser($user);
        }



        $io->success('User Promoted.');

        return Command::SUCCESS;
    }
}
