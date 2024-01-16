<?php

namespace App\Command\User;

use App\Command\ClientTrait;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Response;

#[AsCommand('app:user:create')]
class CreateUserCommand extends Command
{
    use ClientTrait;

    protected function configure(): void
    {
        $this
            ->setDescription('Creates a new user.')
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the user.')
            ->addArgument('email', InputArgument::REQUIRED, 'The email of the user.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $response = $this->client->request('POST', $this->hostUrl.'/user', [
            'body' =>  json_encode([
                'name' => $input->getArgument('name'),
                'email' => $input->getArgument('email'),
            ])
        ]);

        if ($response->getStatusCode() != Response::HTTP_CREATED) {
            $output->writeln('<error>User creation error</error>');

            return Command::FAILURE;
        }

        $output->writeln('<info>Done.</info>');

        $data = json_decode($response->getContent(), true);
        $table = (new Table($output))
            ->setHeaders(['ID', 'Name', 'e-mail'])
            ->setRows([
                [
                    $data['id'],
                    $data['name'],
                    $data['email'],
                ],
            ]);
        $table->render();

        return Command::SUCCESS;
    }
}
