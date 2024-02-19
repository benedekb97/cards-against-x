<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231222103339 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE deck_import ADD deck_id BIGINT DEFAULT NULL');
        $this->addSql('ALTER TABLE deck_import ADD CONSTRAINT FK_AD3A6C0E111948DC FOREIGN KEY (deck_id) REFERENCES deck (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_AD3A6C0E111948DC ON deck_import (deck_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE deck_import DROP FOREIGN KEY FK_AD3A6C0E111948DC');
        $this->addSql('DROP INDEX UNIQ_AD3A6C0E111948DC ON deck_import');
        $this->addSql('ALTER TABLE deck_import DROP deck_id');
    }
}
