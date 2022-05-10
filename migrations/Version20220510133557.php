<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220510133557 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE api_integration (id INT AUTO_INCREMENT NOT NULL, type INT NOT NULL, enabled TINYINT(1) NOT NULL, config JSON NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE api_integration_user (api_integration_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_FA934E067A247F54 (api_integration_id), INDEX IDX_FA934E06A76ED395 (user_id), PRIMARY KEY(api_integration_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE api_integration_category (id INT AUTO_INCREMENT NOT NULL, api_integration_id INT DEFAULT NULL, category_id INT NOT NULL, external_id INT NOT NULL, INDEX IDX_F85A5C7D7A247F54 (api_integration_id), INDEX IDX_F85A5C7D12469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE api_integration_task (id INT AUTO_INCREMENT NOT NULL, api_integration_id INT NOT NULL, task_id INT NOT NULL, external_id INT NOT NULL, INDEX IDX_257E436A7A247F54 (api_integration_id), INDEX IDX_257E436A8DB60186 (task_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE api_integration_user ADD CONSTRAINT FK_FA934E067A247F54 FOREIGN KEY (api_integration_id) REFERENCES api_integration (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE api_integration_user ADD CONSTRAINT FK_FA934E06A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE api_integration_category ADD CONSTRAINT FK_F85A5C7D7A247F54 FOREIGN KEY (api_integration_id) REFERENCES api_integration (id)');
        $this->addSql('ALTER TABLE api_integration_category ADD CONSTRAINT FK_F85A5C7D12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE api_integration_task ADD CONSTRAINT FK_257E436A7A247F54 FOREIGN KEY (api_integration_id) REFERENCES api_integration (id)');
        $this->addSql('ALTER TABLE api_integration_task ADD CONSTRAINT FK_257E436A8DB60186 FOREIGN KEY (task_id) REFERENCES task (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE api_integration_user DROP FOREIGN KEY FK_FA934E067A247F54');
        $this->addSql('ALTER TABLE api_integration_category DROP FOREIGN KEY FK_F85A5C7D7A247F54');
        $this->addSql('ALTER TABLE api_integration_task DROP FOREIGN KEY FK_257E436A7A247F54');
        $this->addSql('DROP TABLE api_integration');
        $this->addSql('DROP TABLE api_integration_user');
        $this->addSql('DROP TABLE api_integration_category');
        $this->addSql('DROP TABLE api_integration_task');
    }
}
