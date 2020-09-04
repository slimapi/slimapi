<?php

declare(strict_types=1);

namespace SlimAPI\Validation;

use SlimAPI\Validation\Generator\GeneratorInterface as Generator;
use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Command extends BaseCommand
{
    private ?Generator $generator = null;

    public function setGenerator(Generator $generator): self
    {
        $this->generator = $generator;
        return $this;
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($this->generator === null) {
            $output->writeln('<error>Missing generator, please use setGenerator() before command registration.</error>');
            return 1;
        }

        $this->generator->generateSchemaList();
        $output->writeln(sprintf(
            '<info>Validation schema (%s) has been generated.</info>',
            $this->generator->getCacheFileName(),
        ));

        return 0;
    }

    protected function configure(): void
    {
        $this
            ->setName('validation:generate')
            ->setDescription('Generate validation schema cache-file for application.');

        parent::configure();
    }
}
