<?php

declare(strict_types=1);

namespace App\tracking\api\v1\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190330143556 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE platform_placements_users DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE platform_placements_users ADD platform_id INT NOT NULL');
        $this->addSql('ALTER TABLE platform_placements_users ADD CONSTRAINT FK_887A20DEFFE6496F FOREIGN KEY (platform_id) REFERENCES platform (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE platform_placements_users ADD CONSTRAINT FK_887A20DE9395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_887A20DEFFE6496F ON platform_placements_users (platform_id)');
        $this->addSql('CREATE INDEX IDX_887A20DE9395C3F3 ON platform_placements_users (customer_id)');
        $this->addSql('ALTER TABLE platform_placements_users ADD PRIMARY KEY (platform_id, customer_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE platform_placements_users DROP FOREIGN KEY FK_887A20DEFFE6496F');
        $this->addSql('ALTER TABLE platform_placements_users DROP FOREIGN KEY FK_887A20DE9395C3F3');
        $this->addSql('DROP INDEX IDX_887A20DEFFE6496F ON platform_placements_users');
        $this->addSql('DROP INDEX IDX_887A20DE9395C3F3 ON platform_placements_users');
        $this->addSql('ALTER TABLE platform_placements_users DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE platform_placements_users DROP platform_id');
        $this->addSql('ALTER TABLE platform_placements_users ADD PRIMARY KEY (customer_id)');
    }
}
