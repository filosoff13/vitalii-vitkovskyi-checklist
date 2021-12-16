<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\UserService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CreateAdminCommand extends Command
{
    protected static $defaultName = 'app:create-admin';
    protected static $defaultDescription = 'Create admin user';
    private UserService $userService;

    public function __construct(UserService $userService, string $name = null)
    {
        parent::__construct($name);
        $this->userService = $userService;
    }

    protected function configure(): void
    {
        $this
            ->addOption('username', 'u', InputOption::VALUE_REQUIRED, 'User name')
            ->addOption('password', 'p', InputOption::VALUE_REQUIRED, 'User password')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $userName = $input->getOption('username');
        $password = $input->getOption('password');

        try {
            $this->userService->createAndFlush($password, $userName);
            $io->success('Congratulation! Admin user successfully created.');
        } catch (\Exception $e){
            $io->error($e->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
