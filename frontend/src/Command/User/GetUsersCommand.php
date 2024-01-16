<?php

namespace App\Command\User;

use App\Command\ClientTrait;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('app:user:list')]
class GetUsersCommand extends Command
{
    use ClientTrait;

    protected function configure(): void
    {
        $this->setDescription('Get users list.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([
            'Users',
            '============',
        ]);

        $response = $this->client->request('GET', $this->hostUrl.'/users');
        $data = json_decode($response->getContent(), true);

        if (!$data) {
            $output->writeln('No users found');

            return Command::SUCCESS;
        }

        $rows = [];
        foreach ($data as $item) {
            $rows[] = [
                $item['id'],
                $item['name'],
                $item['email'],
            ];
        }


        $table = (new Table($output))
            ->setHeaders(['ID', 'Name', 'e-mail'])
            ->setRows($rows);
        $table->render();

        return Command::SUCCESS;
    }
}
