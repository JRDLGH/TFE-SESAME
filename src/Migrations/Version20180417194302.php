<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180417194302 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE tag (id INT AUTO_INCREMENT NOT NULL, keyword VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE gesture (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, profile_video VARCHAR(255) DEFAULT NULL, video VARCHAR(255) DEFAULT NULL, cover VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, creation_date DATETIME DEFAULT CURRENT_TIMESTAMP, is_published TINYINT(1) NOT NULL, publication_date DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE gesture_tag (gesture_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_25C4707B1B8E511 (gesture_id), INDEX IDX_25C4707BAD26311 (tag_id), PRIMARY KEY(gesture_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, username_canonical VARCHAR(180) NOT NULL, email VARCHAR(180) NOT NULL, email_canonical VARCHAR(180) NOT NULL, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) DEFAULT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, confirmation_token VARCHAR(180) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D64992FC23A8 (username_canonical), UNIQUE INDEX UNIQ_8D93D649A0D96FBF (email_canonical), UNIQUE INDEX UNIQ_8D93D649C05FB297 (confirmation_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE gesture_tag ADD CONSTRAINT FK_25C4707B1B8E511 FOREIGN KEY (gesture_id) REFERENCES gesture (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE gesture_tag ADD CONSTRAINT FK_25C4707BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE gesture_tag DROP FOREIGN KEY FK_25C4707BAD26311');
        $this->addSql('ALTER TABLE gesture_tag DROP FOREIGN KEY FK_25C4707B1B8E511');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE gesture');
        $this->addSql('DROP TABLE gesture_tag');
        $this->addSql('DROP TABLE `user`');
    }
}
