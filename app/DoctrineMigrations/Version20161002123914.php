<?php

namespace Application\Migrations;

use MigrationsBundle\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use UserBundle\Entity\Gender;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161002123914 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        parent::up($schema);

        $this->addSql('CREATE TABLE gender (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(250) NOT NULL, show_seeking TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        parent::down($schema);

        $this->addSql('DROP TABLE gender');
    }

    /**
     * @param Schema $schema
     */
    public function postUp(Schema $schema)
    {
        parent::postUp($schema);

        foreach(Gender::$genders as $gender) {
            $genderEntity = new Gender();
            $genderEntity->setName($gender);
            $genderEntity->setShowSeeking(
                (strpos($gender, "Trans") !== false)
                    ? false
                    : true
            );

            $this->em->persist($genderEntity);
        }

        $this->em->flush();
    }
}
