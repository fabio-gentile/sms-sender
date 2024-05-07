<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240502091233 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE sms_translation (id INT AUTO_INCREMENT NOT NULL, sms_id INT NOT NULL, language VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, INDEX IDX_6DEA6DF4BD5C7E60 (sms_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sms_translation ADD CONSTRAINT FK_6DEA6DF4BD5C7E60 FOREIGN KEY (sms_id) REFERENCES sms (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sms_translation DROP FOREIGN KEY FK_6DEA6DF4BD5C7E60');
        $this->addSql('DROP TABLE sms_translation');
    }
}
