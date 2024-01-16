<?php

namespace App\Command\Group;

use App\Command\ClientTrait;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Response;

#[AsCommand('app:group:create')]
class CreateGroupCommand extends Command
{
    use ClientTrait;

    protected function configure(): void
    {
        $this
            ->setDescription('Creates a new group.')
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the group.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $response = $this->client->request(
            'POST',
            sprintf('%s/group', $this->hostUrl),
            [
                'body' =>  json_encode([
                'name' => $input->getArgument('name'),
            ])
        ]);

        if ($response->getStatusCode() != Response::HTTP_CREATED) {
            $output->writeln('<error>Group creation error</error>');

            return Command::FAILURE;
        }

        $output->writeln('<info>Done.</info>');

        $data = json_decode($response->getContent(), true);
        $table = (new Table($output))
            ->setHeaders(['ID', 'Name'])
            ->setRows([
                [
                    $data['id'],
                    $data['name'],
                ],
            ]);
        $table->render();

        return Command::SUCCESS;
    }
}
