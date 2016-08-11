<?php

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use MigrationsBundle\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160215163307 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        parent::up($schema);

        $this->addSql('CREATE TABLE `sessions` (
            `sess_id` VARBINARY(128) NOT NULL PRIMARY KEY,
            `sess_data` BLOB NOT NULL,
            `sess_time` INTEGER UNSIGNED NOT NULL,
            `sess_lifetime` MEDIUMINT NOT NULL
        ) COLLATE utf8_bin, ENGINE = InnoDB;');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        parent::down($schema);

        $this->addSql('DROP TABLE sessions');
    }
}
