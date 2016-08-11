<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160215163306 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user_password (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, password VARCHAR(255) NOT NULL, date_added DATETIME NOT NULL, current INT NOT NULL, INDEX IDX_D54FA2D5A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_email (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, email_address VARCHAR(255) NOT NULL, date_added DATETIME NOT NULL, current INT NOT NULL, INDEX IDX_550872CA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_profiles (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, birth_date DATETIME NOT NULL, gender VARCHAR(50) NOT NULL, username VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_sessions (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, date_added DATETIME NOT NULL, valid INT NOT NULL, keep_alive INT NOT NULL, session_id LONGBLOB NOT NULL, INDEX IDX_7AED7913A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_password ADD CONSTRAINT FK_D54FA2D5A76ED395 FOREIGN KEY (user_id) REFERENCES user_profiles (id)');
        $this->addSql('ALTER TABLE user_email ADD CONSTRAINT FK_550872CA76ED395 FOREIGN KEY (user_id) REFERENCES user_profiles (id)');
        $this->addSql('ALTER TABLE user_sessions ADD CONSTRAINT FK_7AED7913A76ED395 FOREIGN KEY (user_id) REFERENCES user_profiles (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user_password DROP FOREIGN KEY FK_D54FA2D5A76ED395');
        $this->addSql('ALTER TABLE user_email DROP FOREIGN KEY FK_550872CA76ED395');
        $this->addSql('ALTER TABLE user_sessions DROP FOREIGN KEY FK_7AED7913A76ED395');
        $this->addSql('DROP TABLE user_password');
        $this->addSql('DROP TABLE user_email');
        $this->addSql('DROP TABLE user_profiles');
        $this->addSql('DROP TABLE user_sessions');
    }
}
