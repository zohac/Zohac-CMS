<?php

namespace App\Command;

use App\Command\src\Traits\CommandTrait;
use Doctrine\ORM\EntityManagerInterface;
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

class InstallCommand extends Command
{
    private const ENTITY_CLASS = 'entity-class';

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var SymfonyStyle
     */
    private $io;

    /**
     * InstallCommand constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param string|null $name
     */
    public function __construct(EntityManagerInterface $entityManager, string $name = null)
    {
        parent::__construct($name);

        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
        $this
            ->setName('zcms:install')
            ->setDescription('Installation of the Z-CMS')
            ->setHelp('Installation of the Z-CMS');
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
     * @throws src\Exception\CrudException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $confirmQuestion = new ConfirmationQuestion('Generate?', true, '/^(y)/i');

        if ($this->io->askQuestion($confirmQuestion)) {
            $this->commandHelper->generate();

            $this->io->success('Operation successful!');

            $this->io->text('<fg=yellow>Check the new Entity\EntityService::getDeleteMessage</>');
            $this->io->text('<fg=yellow>Check the new translation file : translations\Entity\entity.fr.yaml</>');

            return 0;
        }

        return 1;
    }
}
