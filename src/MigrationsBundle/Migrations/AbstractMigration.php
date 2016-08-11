<?php

namespace MigrationsBundle\Migrations;

use Doctrine\DBAL\Driver\Statement;
use Doctrine\DBAL\Migrations\AbstractMigration as BaseMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Zend\Stdlib\ArrayUtils;

abstract class AbstractMigration extends BaseMigration implements ContainerAwareInterface
{
    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    protected $container;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    protected $tableNames = array();

    protected $filesystem;

    protected $backup = array();

    public function setContainer(ContainerInterface $container = null, $manager = null)
    {
        $this->container = $container;

        $this->em = $this->container->get('doctrine')->getManager($manager);

        $this->setUp();
    }

    public function setUp()
    {
        $this->filesystem = $this->container->get('filesystem');
    }

    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql');

        $this->skipIf($this->version->isMigrated(), 'Version has already been migrated, skipping.');
    }

    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql');

        $this->skipIf(!$this->version->isMigrated(), 'Version is not migrated, skipping.');
    }

    /**
     * Adds query to queue if $addToQueue is true, or executes query and returns the Statement object.
     *
     * @param string $sql        SQL to execute.
     * @param array  $params     Parameters to inject into SQL, optional.
     * @param array  $types      Types of the parameters, optional.
     * @param bool   $addToQueue Whether to add to the SQL queue of migration or to execute
     *                           immediately.
     *
     * @return bool|Statement Returns true if $addToQueue is true, or the Statement object
     *                        returned by Connection::executeQuery if not adding to queue.
     */
    protected function executeQuery($sql, $params = array(), $types = array(), $addToQueue = true)
    {
        if ($addToQueue) {
            // Call Version::addSql to add query to the queue, which is run when up() is finished.
            $this->addSql($sql, $params, $types);

            return true;
        }

        // If not adding to queue, return the Statement object returned from Connection::executeQuery
        return $this->connection->executeQuery($sql, $params, $types);
    }

    /**
     * Finds the ID of an entity that matches $conditions. If no ID is found migration will
     * be aborted if $abortIfNotFound is true, otherwise it will return false.
     *
     * @param string $className
     * @param array  $conditions
     * @param bool   $abortIfNotFound Default: true
     *
     * @return mixed
     */
    protected function findIdBy($className, array $conditions, $abortIfNotFound = true)
    {
        $ids = $this->findIdsBy($className, $conditions, 1, 0, array(), $abortIfNotFound);

        if (!empty($ids)) {
            return $ids[0];
        }

        return false;
    }

    /**
     * Finds the IDs for entities of specified class that match $conditions. If no IDs are found migration will
     * be aborted if $abortIfNotFound is true, otherwise it will return false.
     *
     * @param string $className
     * @param array  $conditions
     * @param int    $limit           Default: null
     * @param int    $offset          Default: null
     * @param array  $orderBy         Default: empty array
     * @param bool   $abortIfNotFound Default: true
     *
     * @return mixed
     */
    protected function findIdsBy($className, array $conditions, $limit = null, $offset = null, array $orderBy = array(), $abortIfNotFound = true)
    {
        $qb = $this->em
            ->createQueryBuilder()
            ->select('e.id AS id')
            ->from($className, 'e');

        // Build where statement from conditions
        foreach ($conditions as $field => $value) {
            $valueKey = 'field_'.$field.'_value';

            $qb->andWhere($qb->expr()->eq('e.'.$field, ':'.$valueKey))
                ->setParameter($valueKey, $value);
        }

        // Build orderBy
        if (!empty($orderBy)) {
            // If associative array, it's in form fieldName => order
            if (ArrayUtils::hasStringKeys($orderBy)) {
                foreach ($orderBy as $field => $order) {
                    $qb->addOrderBy('e.'.$field, $order);
                }
            } else {
                foreach ($orderBy as $field) {
                    $qb->addOrderBy('e.'.$field);
                }
            }
        }

        // Add limit and offset, if set
        if (is_int($limit)) {
            $qb->setMaxResults($limit);

            if (is_int($offset)) {
                $qb->setFirstResult($offset);
            }
        }

        $result = $qb->getQuery()->getResult();

        if (!empty($result)) {
            $ids = array();
            foreach ($result as $row) {
                $ids[] = $row['id'];
            }

            return $ids;
        }

        // No results found, abort or return false.
        if ($abortIfNotFound) {
            $this->abortIf(true, 'Value "'.$value.'" not found for field "'.$className.'.'.$field.'"');
        } else {
            return false;
        }
    }

    /**
     * Returns the table name for the given class. If class not found and $abortIfNotFound is true,
     * the current migration will be aborted. Otherwise, it will return false.
     *
     * @param string $className
     * @param bool   $abortIfNotFound
     *
     * @return string|bool
     */
    protected function getTableName($className, $abortIfNotFound = true)
    {
        if (isset($this->tableNames[$className])) {
            return $this->tableNames[$className];
        }

        $metadata = $this->em->getClassMetadata($className);

        if ($metadata) {
            $this->tableNames[$className] = $metadata->getTableName();

            return $this->tableNames[$className];
        }

        if ($abortIfNotFound) {
            $this->abortIf(true, 'Table not found for class: '.$className);
        } else {
            return false;
        }
    }

    protected function getBackupDir()
    {
        $refl = new \ReflectionClass($this);

        return dirname($refl->getFileName()).'/backup_storage';
    }

    protected function getBackupFileName()
    {
        return $this->getBackupDir().'/'.$this->getClassShortName().'.bak';
    }

    protected function clearBackup()
    {
        $filename = $this->getBackupFileName();
        if ($this->filesystem->exists($filename)) {
            $this->filesystem->remove($filename);
        }
    }

    protected function saveBackup()
    {
        $filename = $this->getBackupFileName();
        $dir = dirname($filename);

        if (!$this->filesystem->exists($dir)) {
            try {
                $this->filesystem->mkdir($dir);
            } catch (IOException $e) {
                $this->write('An error occurred while creating the backup directory: '.$e->getMessage());

                return false;
            }
        }

        $content = serialize($this->backup);

        return false !== file_put_contents($filename, $content);
    }

    protected function loadBackup()
    {
        $filename = $this->getBackupFileName();
        if (!$this->filesystem->exists($filename)) {
            $this->write('Backup file not found: '.$filename);

            return false;
        }

        $contents = file_get_contents($filename);

        if ($contents !== false) {
            $this->backup = unserialize($contents);

            if ($this->backup !== false) {
                return true;
            }
        }

        $this->write('Error loading backup file: '.$filename);

        return false;
    }

    protected function getClassShortName()
    {
        $refl = new \ReflectionClass(get_class($this));

        return $refl->getShortName();
    }

    /**
     * @param Schema $schema
     * @param string $tableName
     * @param string $sql
     * @param bool   $checkIfEmpty If true, only run SQL is the table is empty
     */
    protected function addSqlIfTableExists(Schema $schema, $tableName, $sql, $checkIfEmpty = true)
    {
        if ($schema->hasTable($tableName)) {
            if ($checkIfEmpty) {
                $stmt = $this->executeQuery('SELECT * FROM '.$tableName.' LIMIT 1', array(), array(), false);
                if ($stmt->rowCount() > 0) {
                    $this->write('Skipping SQL on table '.$tableName.' since it is not empty.');

                    return;
                }
            }

            $this->addSql($sql);
        }
    }
}
