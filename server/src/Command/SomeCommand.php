<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use App\Services\StrategyContainer;

#[AsCommand(
    name: 'app:do-something',
    description: 'Do some cooll stuff',
    hidden: false,
    aliases: ['app:do-something']
)]
class SomeCommand extends Command
{
    public function __construct(
        private readonly StrategyContainer $strategyContainer,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setHelp('This command allows you to do some cool stuff')
            ->addArgument('somedata', InputArgument::REQUIRED, 'some data');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([
            'User Creator',
            '============',
            '',
        ]);

        $strategy = $this->strategyContainer->getStrategy('cli');

        $strategy->setStrategy($input->getArgument('somedata'));

        $output->writeln('Whoa!');
        $output->writeln('Username: ' . $strategy->resolve());

        return Command::SUCCESS;
    }
}
