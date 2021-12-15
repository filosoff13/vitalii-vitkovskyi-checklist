<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CreateAdminCommand extends Command
{
    protected static $defaultName = 'app:create-admin';
    protected static $defaultDescription = 'Create admin user';

    protected function configure(): void
    {
        $this
//            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('username', 'u', InputOption::VALUE_REQUIRED, 'User name')
            ->addOption('password', 'p', InputOption::VALUE_REQUIRED, 'User password')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $userName = $input->getOption('username');
        $password = $input->getOption('password');


        $output->writeln('opt1 ' . $input->getOption('username'));
        $output->writeln('opt2 ' . $input->getOption('password'));

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
