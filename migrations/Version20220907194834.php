<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\TaskStatus;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220907194834 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE task_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE task_status_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE task (id SERIAL, status_id INT NOT NULL, text TEXT NOT NULL, created TIMESTAMP(0) WITH TIME ZONE NOT NULL, updated TIMESTAMP(0) WITH TIME ZONE NOT NULL, deleted TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_527EDB256BF700BD ON task (status_id)');
        $this->addSql('CREATE TABLE task_status (id SERIAL, name VARCHAR(31) NOT NULL, created TIMESTAMP(0) WITH TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_40A9E1CF5E237E06 ON task_status (name)');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB256BF700BD FOREIGN KEY (status_id) REFERENCES task_status (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function postUp(Schema $schema): void
    {
        foreach ([TaskStatus::CREATED, TaskStatus::DONE] as $taskStatus) {
            $this->connection->insert('task_status', ['name' => $taskStatus], ['name' => ParameterType::STRING]);
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE task_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE task_status_id_seq CASCADE');
        $this->addSql('ALTER TABLE task DROP CONSTRAINT FK_527EDB256BF700BD');
        $this->addSql('DROP TABLE task');
        $this->addSql('DROP TABLE task_status');
    }
}
