<?php

namespace App\DependencyInjection\Manager;

use App\Exception\PDOAdapterException;
use PDO;
use PDOStatement;

class PDOAdapter implements DataBaseConnectionInterface
{
    /**
     * A PDO instance.
     *
     * @var PDO|null
     */
    protected $dataBase = null;

    /**
     * The host name for the connexion to the database.
     *
     * @var string
     */
    private $host;

    /**
     * The name of the database.
     *
     * @var string
     */
    private $dbname;

    /**
     * The name of the user for the connexion to the database.
     *
     * @var string
     */
    private $user;

    /**
     * The password for the connexion to the database.
     *
     * @var string
     */
    private $password;

    /**
     * @var PDOStatement
     */
    private $query = null;

    /**
     * Retrieving DB connection configuration, and connection.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        // Recording DB connection data
        $this->host = $config['host'];
        $this->dbname = $config['dbname'];
        $this->user = $config['user'];
        $this->password = $config['password'];

        // DB connection request
        $this->getConnection();
    }

    /**
     * Returns a connection object to the DB by initiating the connection as needed.
     */
    private function getConnection()
    {
        // If the variable is strictly null
        if (null === $this->dataBase) {
            // Create a new connection to DB using PDO
            $this->dataBase = new PDO(
                'mysql:host='.$this->host.';dbname='.$this->dbname.';charset=utf8',
                $this->user,
                $this->password,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        }
    }

    public function addQuery(string $query): DataBaseConnectionInterface
    {
        $this->query = $this->dataBase->prepare($query);

        return $this;
    }

    public function setParameter(string $parameter, $value, $option = null): DataBaseConnectionInterface
    {
        $this->query->bindValue($parameter, $value, $option);

        return $this;
    }

    /**
     * @return iterable
     *
     * @throws PDOAdapterException
     */
    public function execute(): iterable
    {
        if (null === $this->query) {
            throw new PDOAdapterException();
        }

        $this->query->execute();

        if ($response = $this->query->fetch()) {
            return $response;
        }

        return [];
    }
}
