<?php

namespace App\EntityManager\PersistenceBackend;


use App\EntityManager\Entity\EntityInterface;
use App\EntityManager\Exception\FileSystemIOException;
use App\ValueObject\ValueObjectInterface;
use Psr\Log\LoggerInterface;

class FileSystemPersistenceBackend
    implements PersistenceBackendInterface
{
    private string $directory = 'var/db/';

    /**
     * reversed index aligned by primary key and they uniqId() for filename
     * $this->tablespace[$tableName] = [
     *      'sku-555-a-primary-key' => 'uniqId()',
     *      'sku-546' => 'uniqId()'
     * ];
     *
     */
    private array $tablespaces;

    public function __construct(
        private readonly LoggerInterface $logger,
        private ConfigurableIndex $indexConfig
    )
    {
        $this->tablespaces = [];
        $this->initFileSystemStorage();
        $this->onWakeup();

        $this->logger->debug(':: PersistenceBackend constructed.');
    }

    public function __destruct()
    {
        $this->onSleep();

        $this->logger->debug(':: PersistenceBackend de constructed.');
    }

    public function getIndexSize(string $indexName): void
    {
        $filesize = filesize($this->directory . $indexName . ".index");
        if ($filesize > pow(1024, 2)) {
            // 1MB index file ~ about 20k dummy classes
            // file_index is md5: 32 5d41402abc4b2a76b9719d911017c592
            $this->logger->debug($indexName . " $filesize, kb, mb, ...");
        }
    }

    public function put(EntityInterface $baseEntity): bool|int
    {
        //$hashedFilename = hash('md5', serialize($baseEntity));
        $bytesLength = file_put_contents($this->directory . "instances/" . $baseEntity->getUniqId(),
            serialize($baseEntity));

        if ($bytesLength) {
            $this->tablespaces[$baseEntity::class][$baseEntity->getPrimaryKey()->value()]
                = $baseEntity->getUniqId()->value();
        }

        return $bytesLength;
    }

    public function get(ValueObjectInterface $primaryKey): ?EntityInterface
    {
        // todo need to fix scan all tables
        // we can add tablespace over param for this method,
        // $this->get(string $indexedClassname, ValueObject $primaryKey)

        foreach ($this->indexConfig->getConfiguration() as $indexedEntityClassname => $voidPropertyName) {
            /** @var array $container */
            $container = $this->tablespaces[$indexedEntityClassname];
            if (array_key_exists($primaryKey->value(), $container)) {

                $data = file_get_contents($this->directory . "instances/"
                    . $this->tablespaces[$indexedEntityClassname][$primaryKey->value()]);

                if (strlen($data)) {
                    return unserialize($data);
                }

                throw new FileSystemIOException(
                    "Data store file $indexedEntityClassname,"
                                . 'has broken $voId in index, ' . $primaryKey->value()
                    );
            }
        }

        return null;
    }

    /**
     * todo, probably has issue with singletons in swoole runtime
     * @return void
     */
    private function onWakeup(): void
    {
        if (file_exists($this->directory . 'index.config')) {
            if ($cfg = file_get_contents($this->directory . 'index.config')) {
                $this->indexConfig = new ConfigurableIndex(json_decode($cfg, true));
            }
        }

        foreach ($this->indexConfig->getConfiguration() as $index => $config) {
            $this->tablespaces[$index] = [];
            $alias = $this->alias($index);
            $indexPath = $this->directory . $alias . '.index';
            if (file_exists($indexPath)) {
                $tablespace = file_get_contents($indexPath);
                if ($tablespace && strlen($tablespace)) {
                    $this->tablespaces[$index] = unserialize($tablespace);
                }
            }
        }

        $this->logger->debug(':: PersistenceBackend wakeup successfully.');
    }

    /**
     * todo, probably has issue with singletons in swoole runtime
     * @return void
     */
    private function onSleep(): void
    {
        if ($len = file_put_contents($this->directory . 'index.config', json_encode($this->indexConfig->getConfiguration()))) {

            foreach ($this->indexConfig->getConfiguration() as $index => $config) {
                $indexData = serialize($this->tablespaces[$index]);
                $alias = $this->alias($index);
                if ($len = file_put_contents($this->directory . $alias . '.index', $indexData)) {
                    $this->logger->debug(':: PersistenceBackend :: Index:' . $alias . ' saved.');
                }
            }
        }
    }

    private function alias(string $class): string
    {
        $label = explode("\\", $class);
        if (count($label) > 1) {
            $label = array_reverse($label);

            return $label[0];
        }

        return $class;
    }

    private function initFileSystemStorage(): void
    {
        // created secure db storage for 777 nobody
        if (!file_exists($this->directory)) {
            mkdir($this->directory);
        }

        // create file that will save hashmap index
        foreach ($this->indexConfig->getConfiguration() as $indexedEntity => $primaryKeys) {
            $indexFilename = $this->directory
                . $this->alias($indexedEntity)
                . ".index";

            if (!file_exists($indexFilename)) {
                /*
                $resource = fopen($indexFilename, 'w+');
                if ($ok = fclose($resource)) { }
                */
            }

            if (!is_readable($indexFilename) || !is_writable($indexFilename)) {
                throw new FileSystemIOException("Data store file $indexFilename must be readable/writable.");
            }
        }

        // folder for files with serialized records
        if (!file_exists($this->directory . 'instances')) {
            // created secure db/indexName folder for anybody
            mkdir($this->directory . 'instances');
        }
    }

    private function isKnownPrimaryKey(ValueObjectInterface $voId): bool
    {
        $isKnownPrimaryKey = false;

        foreach ($this->tablespaces as $tablespace => $voIdCfg) {
            $isKnownPrimaryKey = array_key_exists($this->tablespaces[$tablespace], $voId->value());
        }

        $this->logger->debug('index contains primary key: ', (array)$voId);

        return $isKnownPrimaryKey;
    }

    public function setConfiguration(ConfigurableIndex $indexConfig): void
    {
        $this->indexConfig = $indexConfig;
        $this->logger->debug(':: PersistenceBackend configuration successfully set.');

        // configuration has issue with recall of wakeup method on configuration has changes
        $this->onWakeup();
    }

    public function unlink(EntityInterface $entity): bool
    {
        if (unlink($this->directory . 'instances/' . $entity->getUniqId()))
        {
            unset($this->tablespaces[$entity::class][$entity->getPrimaryKey()->value()]);

            return true;
        }

        return false;
    }
}
