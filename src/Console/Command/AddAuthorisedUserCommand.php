<?php

declare(strict_types=1);

namespace App\Console\Command;

use App\Service\AuthorisedUserService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddAuthorisedUserCommand extends Command
{
    public function __construct(
        private readonly AuthorisedUserService $service,
    )
    {
        parent::__construct();
    }

    public function configure(): void
    {
        $this->setName('app:authorise-user');

        $this->addArgument('userId', InputArgument::REQUIRED, 'ID of the user you want to authorise');
        $this->addArgument('domain', InputArgument::REQUIRED, 'Domain to add the user to');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $userId = $input->getArgument('userId');
        $domain = $input->getArgument('domain');

        $this->service->authoriseUser($userId, $domain);

        $output->writeln('User has been successfully authorised!');

        return self::SUCCESS;
    }
}