<?php

declare(strict_types=1);

namespace UserApi\Application\Command;

use Random\Engine\Secure;
use Random\Randomizer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use UserApi\Domain\Entity\User;
use UserApi\Domain\Repository\UserRepository;
use UserApi\Domain\ValueObject\Instant;
use UserApi\Domain\ValueObject\PasswordHash;

use function assert;
use function bin2hex;
use function is_string;
use function sprintf;

class UserCreateCommand extends Command
{
    public function __construct(
        private UserRepository $userRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('user:create');
        $this->setDescription('Create a user');
        $this->addArgument('name', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument('name');
        assert(is_string($name));

        $randomizer = new Randomizer(new Secure());
        $password   = bin2hex($randomizer->getBytes(16));

        $now = Instant::now();

        $user = new User(
            $this->userRepository->generateId(),
            $name,
            PasswordHash::fromPlainPassword($password),
            $now,
            $now,
        );

        $this->userRepository->create($user);

        $output->writeln(sprintf(
            'Created User with ID %s and password %s',
            $user->id->toString(),
            $password,
        ));

        return Command::SUCCESS;
    }
}
