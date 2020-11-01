<?php


namespace App\DependencyInjection\Compiler;


use App\DependencyInjection\Manager\ParameterManager;
use App\DependencyInjection\Manager\PDOAdapter;
use App\Entity\Parameter;
use App\Repository\ParameterRepository;
use Exception;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ParametersCompilerPass implements CompilerPassInterface
{
    /**
     * @var ParameterManager
     */
    private $parameterManager;

    public function __construct()
    {
        $dataBaseConnection = new PDOAdapter([]);
        $this->parameterManager = new ParameterManager($dataBaseConnection);
    }

    /**
     * @inheritDoc
     *
     * @throws Exception
     */
    public function process(ContainerBuilder $container)
    {
        $themeFolderParameter = $this->parameterManager->findOneByName('theme_folder');
        $container->setParameter('theme_folder', $themeFolderParameter->getValue());
    }
}