<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240426094934 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE sms_reference (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, sms_id INT DEFAULT NULL, status VARCHAR(255) NOT NULL, INDEX IDX_D9C28FA4A76ED395 (user_id), INDEX IDX_D9C28FA4BD5C7E60 (sms_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sms_reference ADD CONSTRAINT FK_D9C28FA4A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE sms_reference ADD CONSTRAINT FK_D9C28FA4BD5C7E60 FOREIGN KEY (sms_id) REFERENCES sms (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sms_reference DROP FOREIGN KEY FK_D9C28FA4A76ED395');
        $this->addSql('ALTER TABLE sms_reference DROP FOREIGN KEY FK_D9C28FA4BD5C7E60');
        $this->addSql('DROP TABLE sms_reference');
    }
}
