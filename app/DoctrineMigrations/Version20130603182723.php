<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130603182723 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("
               ALTER TABLE  `course` CHANGE  `state`  `state` ENUM(  'editing',  'published',  'closed' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  'published' COMMENT  '课程状态'
            ");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}