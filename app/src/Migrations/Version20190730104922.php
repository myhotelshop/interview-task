<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190730104922 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE conversions (id INT AUTO_INCREMENT NOT NULL, booking_number VARCHAR(255) NOT NULL, customer_id VARCHAR(255) NOT NULL, platform VARCHAR(255) NOT NULL, revenue INT NOT NULL, conversation_date DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE revenue_distributions (id INT AUTO_INCREMENT NOT NULL, conversion_id INT NOT NULL, platform VARCHAR(255) NOT NULL, amount INT NOT NULL, INDEX IDX_7619AA3F4C1FF126 (conversion_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE revenue_distributions ADD CONSTRAINT FK_7619AA3F4C1FF126 FOREIGN KEY (conversion_id) REFERENCES conversions (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE revenue_distributions DROP FOREIGN KEY FK_7619AA3F4C1FF126');
        $this->addSql('DROP TABLE conversions');
        $this->addSql('DROP TABLE revenue_distributions');
    }
}
