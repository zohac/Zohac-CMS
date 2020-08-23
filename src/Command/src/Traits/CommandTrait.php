<?php

namespace App\Command\src\Traits;

use Exception;
use App\Command\src\Helper\CommandHelper;
use Symfony\Bundle\MakerBundle\Validator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

trait CommandTrait
{
    /**
     * @var CommandHelper
     */
    private $commandHelper;

    /**
     * @var SymfonyStyle
     */
    private $io;

    public function __construct(CommandHelper $commandHelper, string $name = null)
    {
        $this->commandHelper = $commandHelper;

        parent::__construct($name);
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @throws Exception
     */
    public function interact(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);

        if (null === $input->getArgument(self::ENTITY_CLASS)) {
            $argument = $this->getDefinition()->getArgument(self::ENTITY_CLASS);

            $entities = $this->commandHelper->getEntitiesForAutocomplete();
            sort($entities);

            $question = new Question($argument->getDescription());
            $question->setAutocompleterValues($entities);

            $question->setValidator(function ($value) use ($entities) {
                return Validator::entityExists($value, $entities);
            });

            $value = $this->io->askQuestion($question);

            $input->setArgument(self::ENTITY_CLASS, $value);
        }
    }
}