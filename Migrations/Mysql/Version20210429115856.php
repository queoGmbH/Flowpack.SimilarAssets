<?php

declare(strict_types=1);

namespace Neos\Flow\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210429115856 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds table to store image hashes for fast comparison';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE flowpack_similarassets_domain_model_imagehash (imageid VARCHAR(255) NOT NULL, hash BIGINT(20) unsigned NOT NULL, PRIMARY KEY(imageid)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE flowpack_similarassets_domain_model_imagehash');
    }
}
