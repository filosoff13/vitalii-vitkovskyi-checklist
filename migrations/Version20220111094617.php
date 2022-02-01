<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220111094617 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE activity DROP FOREIGN KEY FK_AC74095A8DB60186');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095A8DB60186 FOREIGN KEY (task_id) REFERENCES task (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE activity DROP FOREIGN KEY FK_AC74095A8DB60186');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095A8DB60186 FOREIGN KEY (task_id) REFERENCES task (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
