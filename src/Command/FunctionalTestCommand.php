<?php

namespace App\Command;

use App\Command\src\Helper\CommandHelper;
use Exception;
use ReflectionException;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Bundle\MakerBundle\Validator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class FunctionalTestCommand extends Command
{
    private const ENTITY_CLASS = 'entity-class';

    /**
     * @var SymfonyStyle
     */
    private $io;

    /**
     * @var CommandHelper
     */
    private $commandHelper;

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

    protected function configure()
    {
        $this
            ->setName('zcms:make:functional-test')
            ->setDescription('Creates functional test for a controller.')
            ->addArgument(
                self::ENTITY_CLASS,
                InputArgument::OPTIONAL,
                sprintf(
                    'The entity class to create CRUD (e.g. <fg=yellow>Acme\Entity\%s</>)',
                    Str::asClassName(Str::getRandomTerm())
                )
            )
            ->setHelp('Creates functional test for a controller.');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     *
     * @throws LoaderError
     * @throws ReflectionException
     * @throws RuntimeError
     * @throws SyntaxError
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->commandHelper->setEntityClass($input->getArgument(self::ENTITY_CLASS));

        $confirmQuestion = new ConfirmationQuestion('Generate?', true, '/^(y)/i');

        if ($this->io->askQuestion($confirmQuestion)) {
            $this->commandHelper->generateFunctionalTest();

            $this->io->success('Operation successful!');

            $this->io->text('<fg=yellow>Check the new Entity\EntityService::getDeleteMessage</>');
            $this->io->text('<fg=yellow>Check the new translation file : translations\Entity\entity.fr.yaml</>');

            return 0;
        }

        return 1;
    }
}
