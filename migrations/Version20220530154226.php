<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220530154226 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE api_integration_category DROP FOREIGN KEY FK_F85A5C7D12469DE2');
        $this->addSql('ALTER TABLE api_integration_category ADD CONSTRAINT FK_F85A5C7D12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE api_integration_task DROP FOREIGN KEY FK_257E436A8DB60186');
        $this->addSql('ALTER TABLE api_integration_task ADD CONSTRAINT FK_257E436A8DB60186 FOREIGN KEY (task_id) REFERENCES task (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE api_integration_category DROP FOREIGN KEY FK_F85A5C7D12469DE2');
        $this->addSql('ALTER TABLE api_integration_category ADD CONSTRAINT FK_F85A5C7D12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE api_integration_task DROP FOREIGN KEY FK_257E436A8DB60186');
        $this->addSql('ALTER TABLE api_integration_task ADD CONSTRAINT FK_257E436A8DB60186 FOREIGN KEY (task_id) REFERENCES task (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
