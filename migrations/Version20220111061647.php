<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220111061647 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE task_user (task_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_FE2042328DB60186 (task_id), INDEX IDX_FE204232A76ED395 (user_id), PRIMARY KEY(task_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE task_user ADD CONSTRAINT FK_FE2042328DB60186 FOREIGN KEY (task_id) REFERENCES task (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE task_user ADD CONSTRAINT FK_FE204232A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');

        $this->addSql('ALTER TABLE task CHANGE user_id owner_id INT NOT NULL');
        $this->addSql('INSERT INTO task_user(task_id, user_id) SELECT task.id AS task_id, task.owner_id AS user_id FROM task');

        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095A8DB60186 FOREIGN KEY (task_id) REFERENCES task (id)');
        $this->addSql('CREATE INDEX IDX_AC74095A8DB60186 ON activity (task_id)');
        $this->addSql('ALTER TABLE task RENAME INDEX idx_527edb25a76ed395 TO IDX_527EDB257E3C61F9');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE task CHANGE owner_id user_id INT NOT NULL');
        $this->addSql('DROP TABLE task_user');

        $this->addSql('ALTER TABLE activity DROP FOREIGN KEY FK_AC74095A8DB60186');
        $this->addSql('DROP INDEX IDX_AC74095A8DB60186 ON activity');
        $this->addSql('ALTER TABLE task RENAME INDEX idx_527edb257e3c61f9 TO IDX_527EDB25A76ED395');
    }
}
