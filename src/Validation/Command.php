<?php

declare(strict_types=1);

namespace SlimAPI\Validation;

use SlimAPI\Validation\Generator\GeneratorInterface as Generator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Command extends \Symfony\Component\Console\Command\Command
{
    private Generator $generator;

    public function __construct(Generator $generator, string $name = 'validation:generate')
    {
        parent::__construct($name);

        $this->generator = $generator;
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->generator->generateSchemaList();
        $output->writeln(sprintf(
            '<info>Validation schema (%s) has been generated.</info>',
            $this->generator->getCacheFileName(),
        ));

        return 0;
    }

    protected function configure(): void
    {
        $this->setDescription('Generate validation schema cache-file for application.');

        parent::configure();
    }
}
