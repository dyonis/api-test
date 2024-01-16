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

#[AsCommand('app:group:update')]
class UpdateGroupCommand extends Command
{
    use ClientTrait;

    protected function configure(): void
    {
        $this
            ->setDescription('Updates a group.')
            ->addArgument('id', InputArgument::REQUIRED, 'ID of the group.')
            ->addArgument('name', InputArgument::OPTIONAL, 'The name of the group.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $response = $this->client->request(
            'PUT',
            sprintf(
                '%s/group/%s',
                $this->hostUrl,
                $input->getArgument('id')
            ),
            [
                'body' =>  json_encode([
                    'name' => $input->getArgument('name'),
                ])
            ]
        );

        if ($response->getStatusCode() != Response::HTTP_OK) {
            $output->writeln(sprintf('<error>Error updating group %s</error>', $response->getContent()));

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
