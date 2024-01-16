<?php

namespace App\Command\Group;

use App\Command\ClientTrait;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Response;

#[AsCommand('app:group:delete')]
class DeleteGroupCommand extends Command
{
    use ClientTrait;

    protected function configure(): void
    {
        $this
            ->setDescription('Deletes a group.')
            ->addArgument('id', InputArgument::REQUIRED, 'ID of the group.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $response = $this->client->request(
            'DELETE',
            sprintf(
                '%s/group/%s',
                $this->hostUrl,
                $input->getArgument('id')
            )
        );

        if ($response->getStatusCode() != Response::HTTP_NO_CONTENT) {
            $output->writeln('<error>Group deletion error</error>');

            return Command::FAILURE;
        }

        $output->writeln('<info>Done.</info>');

        return Command::SUCCESS;
    }
}
