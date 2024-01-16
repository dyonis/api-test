<?php

namespace App\Command\User;

use App\Command\ClientTrait;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Response;

#[AsCommand('app:user:add-to-group')]
class AddUserToGroupCommand extends Command
{
    use ClientTrait;

    protected function configure(): void
    {
        $this
            ->setDescription('Add user to the group.')
            ->addArgument('userId', InputArgument::REQUIRED, 'ID of the user.')
            ->addArgument('groupId', InputArgument::REQUIRED, 'ID of the group.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $response = $this->client->request('PUT',
            sprintf(
                '%s/user/%s/add-to-group/%s',
                $this->hostUrl,
                $input->getArgument('userId'),
                $input->getArgument('groupId'),
            )
        );

        if ($response->getStatusCode() != Response::HTTP_OK) {
            $output->writeln('<error>Error</error>');

            return Command::FAILURE;
        }

        $output->writeln('<info>Done.</info>');

        return Command::SUCCESS;
    }
}
