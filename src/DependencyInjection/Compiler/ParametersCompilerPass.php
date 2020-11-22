<?php

namespace App\DependencyInjection\Compiler;

use App\DependencyInjection\Manager\ParameterManager;
use App\DependencyInjection\Manager\PDOAdapter;
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
        $config = [
            'host' => 'localhost',
            'dbname' => $_ENV['DB_NAME'],
            'user' => $_ENV['DB_USER'],
            'password' => $_ENV['DB_PASSWORD'],
        ];

        $dataBaseConnection = new PDOAdapter($config);
        $this->parameterManager = new ParameterManager($dataBaseConnection);
    }

    /**
     * {@inheritdoc}
     *
     * @throws Exception
     */
    public function process(ContainerBuilder $container)
    {
        $themeFolderParameter = $this->parameterManager->findOneByName('theme_folder');

        if ($themeFolderParameter) {
            $themeFolder = $themeFolderParameter->getValue();
            $container->setParameter('theme_folder', $themeFolder['name']);
        }
    }
}
