<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260501181125 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `character` (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, experience INT NOT NULL, background VARCHAR(255) DEFAULT NULL, alignment VARCHAR(50) NOT NULL, stats JSON NOT NULL, inventory JSON DEFAULT NULL, lore LONGTEXT DEFAULT NULL, notes LONGTEXT DEFAULT NULL, user_id INT NOT NULL, race_id INT DEFAULT NULL, character_class_id INT DEFAULT NULL, INDEX IDX_937AB034A76ED395 (user_id), INDEX IDX_937AB0346E59D40D (race_id), INDEX IDX_937AB034B201E281 (character_class_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE character_class (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, description LONGTEXT DEFAULT NULL, hit_die INT NOT NULL, primary_ability VARCHAR(50) NOT NULL, saving_throw JSON DEFAULT NULL, features JSON DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE character_export (id INT AUTO_INCREMENT NOT NULL, file_path VARCHAR(255) NOT NULL, char_relation_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_E1CED507ADC1B91E (char_relation_id), INDEX IDX_E1CED507A76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE race (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, description LONGTEXT DEFAULT NULL, stat_bonuses JSON DEFAULT NULL, traits JSON DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, role JSON NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE `character` ADD CONSTRAINT FK_937AB034A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE `character` ADD CONSTRAINT FK_937AB0346E59D40D FOREIGN KEY (race_id) REFERENCES race (id)');
        $this->addSql('ALTER TABLE `character` ADD CONSTRAINT FK_937AB034B201E281 FOREIGN KEY (character_class_id) REFERENCES character_class (id)');
        $this->addSql('ALTER TABLE character_export ADD CONSTRAINT FK_E1CED507ADC1B91E FOREIGN KEY (char_relation_id) REFERENCES `character` (id)');
        $this->addSql('ALTER TABLE character_export ADD CONSTRAINT FK_E1CED507A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `character` DROP FOREIGN KEY FK_937AB034A76ED395');
        $this->addSql('ALTER TABLE `character` DROP FOREIGN KEY FK_937AB0346E59D40D');
        $this->addSql('ALTER TABLE `character` DROP FOREIGN KEY FK_937AB034B201E281');
        $this->addSql('ALTER TABLE character_export DROP FOREIGN KEY FK_E1CED507ADC1B91E');
        $this->addSql('ALTER TABLE character_export DROP FOREIGN KEY FK_E1CED507A76ED395');
        $this->addSql('DROP TABLE `character`');
        $this->addSql('DROP TABLE character_class');
        $this->addSql('DROP TABLE character_export');
        $this->addSql('DROP TABLE race');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
