<?php

namespace App\Command\Group;

use App\Command\ClientTrait;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('app:group:report')]
class GroupsReportCommand extends Command
{
    use ClientTrait;

    protected function configure(): void
    {
        $this->setDescription('Get groups report.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $groups = $this->getGroups();

        if (!$groups) {
            $output->writeln('No groups found');

            return Command::SUCCESS;
        }

        $users = $this->getUsers();


        $rows = [];
        foreach ($groups as $group) {
            $rows[] = [new TableCell($group['name'], ['colspan' => 3])];
            $rows[] = new TableSeparator();

            foreach ($users as $user) {
                if (in_array($group['id'], $user['groups'])) {
                    $rows[] = [
                        $user['id'],
                        $user['name'],
                        $user['email'],
                    ];
                }
            }
            $rows[] = new TableSeparator();
        }


        $table = (new Table($output))
            ->setHeaderTitle('Groups')
            ->setHeaders(['ID', 'Name', 'Email'])
            ->setRows($rows);
        $table->render();

        return Command::SUCCESS;
    }

    private function getGroups(): array
    {
        $response = $this->client->request('GET', $this->hostUrl.'/groups');

        return json_decode($response->getContent(), true) ?: [];
    }

    private function getUsers(): ?array
    {
        $response = $this->client->request('GET', $this->hostUrl.'/users');

        return json_decode($response->getContent(), true) ?: [];
    }
}
