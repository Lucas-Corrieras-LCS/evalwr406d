<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\Migrations\AbstractMigration;

final class Version20260610000001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create marque and vehicule tables (MySQL + PostgreSQL compatible)';
    }

    public function up(Schema $schema): void
    {
        $isPostgres = $this->connection->getDatabasePlatform() instanceof PostgreSQLPlatform;

        if ($isPostgres) {
            $this->addSql('CREATE TABLE marque (
                id SERIAL PRIMARY KEY,
                nom VARCHAR(100) NOT NULL,
                annee_creation INT NOT NULL,
                pays VARCHAR(100) NOT NULL
            )');
            $this->addSql('CREATE TABLE vehicule (
                id SERIAL PRIMARY KEY,
                marque_id INT NOT NULL,
                modele VARCHAR(150) NOT NULL,
                prix NUMERIC(10,2) NOT NULL,
                puissance INT NOT NULL,
                annee INT NOT NULL,
                photo VARCHAR(255) DEFAULT NULL,
                CONSTRAINT fk_vehicule_marque FOREIGN KEY (marque_id) REFERENCES marque(id)
            )');
            $this->addSql('CREATE INDEX idx_vehicule_marque ON vehicule(marque_id)');
        } else {
            $this->addSql('CREATE TABLE marque (
                id INT AUTO_INCREMENT NOT NULL,
                nom VARCHAR(100) NOT NULL,
                annee_creation INT NOT NULL,
                pays VARCHAR(100) NOT NULL,
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
            $this->addSql('CREATE TABLE vehicule (
                id INT AUTO_INCREMENT NOT NULL,
                marque_id INT NOT NULL,
                modele VARCHAR(150) NOT NULL,
                prix NUMERIC(10,2) NOT NULL,
                puissance INT NOT NULL,
                annee INT NOT NULL,
                photo VARCHAR(255) DEFAULT NULL,
                INDEX IDX_marque (marque_id),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
            $this->addSql('ALTER TABLE vehicule ADD CONSTRAINT fk_vehicule_marque FOREIGN KEY (marque_id) REFERENCES marque(id)');
        }
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE vehicule DROP FOREIGN KEY fk_vehicule_marque');
        $this->addSql('DROP TABLE vehicule');
        $this->addSql('DROP TABLE marque');
    }
}
