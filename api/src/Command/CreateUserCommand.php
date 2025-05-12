<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * This command is used to consume messages from the Danin queue.
 *
 * Thsi command should run continuously in the background to process messages as they arrive.
 */
#[AsCommand(
    name: 'app:user:create',
    description: 'Create a new user.',
)]
final class CreateUserCommand extends Command
{
    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $passwordHasher,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Create a new user.');
        $this->addArgument('email', InputArgument::OPTIONAL, 'The email of the new user.');
        $this->addArgument('password', InputArgument::OPTIONAL, 'The password of the new user.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');

        $helper = $this->getHelper('question');

        if (!$helper instanceof QuestionHelper) {
            throw new \RuntimeException('Question helper not found.');
        }

        if (null === $email) {
            $question = new Question('Enter the email of user: ', 'John Doe');

            $email = $helper->ask($input, $output, $question);
        }

        if (null === $password) {
            $question = new Question('Enter the password of user: ', 'password');
            $question->setHidden(true);

            $password = $helper->ask($input, $output, $question);
        }

        $user = new User('admin', $email);
        $user->setUsername($email);
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));

        $this->userRepository->save($user);

        $output->writeln('<info>User created successfully.</info>');

        return Command::SUCCESS;
    }
}
