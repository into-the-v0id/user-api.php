<?php

declare(strict_types=1);

namespace UserApi\Infrastructure\Doctrine\Migrations\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230604131358 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Users Table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE users (
                id char(26) NOT NULL PRIMARY KEY,
                name text NOT NULL,
                password_hash text NOT NULL,
                date_created timestamp(8) NOT NULL,
                date_updated timestamp(8) NOT NULL
            );
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE users');
    }
}
