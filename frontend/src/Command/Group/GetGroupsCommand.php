<?php

namespace App\Command\Group;

use App\Command\ClientTrait;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('app:group:list')]
class GetGroupsCommand extends Command
{
    use ClientTrait;

    protected function configure(): void
    {
        $this->setDescription('Get groups list.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([
            'Groups',
            '============',
        ]);

        $response = $this->client->request('GET', $this->hostUrl.'/groups');
        $data = json_decode($response->getContent(), true);

        if (!$data) {
            $output->writeln('No groups found');

            return Command::SUCCESS;
        }

        $rows = [];
        foreach ($data as $item) {
            $rows[] = [
                $item['id'],
                $item['name'],
            ];
        }


        $table = (new Table($output))
            ->setHeaders(['ID', 'Name'])
            ->setRows($rows);
        $table->render();

        return Command::SUCCESS;
    }
}
