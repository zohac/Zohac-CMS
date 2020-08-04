<?php

namespace App\Command;

use App\Command\src\Helper\CrudHelper;
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

class CrudCommand extends Command
{
    /**
     * @var CrudHelper
     */
    private $crudHelper;

    /**
     * @var SymfonyStyle
     */
    private $io;

    public function __construct(CrudHelper $crudHelper, string $name = null)
    {
        $this->crudHelper = $crudHelper;

        parent::__construct($name);
    }

    protected function configure()
    {
        $this
            ->setName('zcms:make:crud')
            ->setDescription('Creates CRUD for Doctrine entity class')
            ->addArgument('entity-class', InputArgument::OPTIONAL, sprintf('The entity class to create CRUD (e.g. <fg=yellow>Acme\DemoBundle\Entity\%s</>)', Str::asClassName(Str::getRandomTerm())))
            ->setHelp('Creates CRUD for Doctrine entity class')
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    public function interact(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);

        if (null === $input->getArgument('entity-class')) {
            $argument = $this->getDefinition()->getArgument('entity-class');

            $entities = $this->crudHelper->getEntitiesForAutocomplete();
            sort($entities);

            $question = new Question($argument->getDescription());
            $question->setAutocompleterValues($entities);

            $question->setValidator(function ($value) use ($entities) {
                return Validator::entityExists($value, $entities);
            });

            $value = $this->io->askQuestion($question);

            $input->setArgument('entity-class', $value);
        }
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     *
     * @throws ReflectionException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->crudHelper->setEntityClass($input->getArgument('entity-class'));

        $confirmQuestion = new ConfirmationQuestion('Generate?', true, '/^(y)/i');

        if ($this->io->askQuestion($confirmQuestion)) {
            $this->crudHelper->generate();

            $this->io->success('Operation successful!');

            return 0;
        }

        return 1;
    }
}
