<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260130131838 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE note (id INT AUTO_INCREMENT NOT NULL, content LONGTEXT DEFAULT NULL, updated_at DATETIME DEFAULT NULL, user_book_id INT NOT NULL, UNIQUE INDEX UNIQ_CFBDFA144EAFAD8B (user_book_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE note ADD CONSTRAINT FK_CFBDFA144EAFAD8B FOREIGN KEY (user_book_id) REFERENCES user_book (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE note DROP FOREIGN KEY FK_CFBDFA144EAFAD8B');
        $this->addSql('DROP TABLE note');
    }
}
