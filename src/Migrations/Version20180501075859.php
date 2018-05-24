<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180501075859 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE deficiency (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, type VARCHAR(50) DEFAULT NULL, severity VARCHAR(50) DEFAULT NULL, UNIQUE INDEX UNIQ_1622D0B25E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE disabled (id INT AUTO_INCREMENT NOT NULL, profile_id INT NOT NULL, firstname VARCHAR(50) NOT NULL, lastname VARCHAR(50) NOT NULL, birthday DATE DEFAULT NULL, UNIQUE INDEX UNIQ_84FE6EE3CCFA12B8 (profile_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE disabled_deficiency (disabled_id INT NOT NULL, deficiency_id INT NOT NULL, INDEX IDX_8DD542876C9E327F (disabled_id), INDEX IDX_8DD54287F4C36BA3 (deficiency_id), PRIMARY KEY(disabled_id, deficiency_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE profile (id INT AUTO_INCREMENT NOT NULL, content VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE profile_gesture (id INT AUTO_INCREMENT NOT NULL, profile_id INT NOT NULL, gesture_id INT NOT NULL, learning_date DATE NOT NULL, INDEX IDX_703E64ACCCFA12B8 (profile_id), INDEX IDX_703E64ACB1B8E511 (gesture_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag (id INT AUTO_INCREMENT NOT NULL, keyword VARCHAR(50) NOT NULL, UNIQUE INDEX UNIQ_389B7835A93713B (keyword), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE gesture (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, profile_video VARCHAR(255) DEFAULT NULL, video VARCHAR(255) DEFAULT NULL, cover VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, creation_date DATETIME DEFAULT CURRENT_TIMESTAMP, is_published TINYINT(1) NOT NULL, publication_date DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_A380BA305E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE gesture_tag (gesture_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_25C4707B1B8E511 (gesture_id), INDEX IDX_25C4707BAD26311 (tag_id), PRIMARY KEY(gesture_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, username_canonical VARCHAR(180) NOT NULL, email VARCHAR(180) NOT NULL, email_canonical VARCHAR(180) NOT NULL, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) DEFAULT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, confirmation_token VARCHAR(180) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D64992FC23A8 (username_canonical), UNIQUE INDEX UNIQ_8D93D649A0D96FBF (email_canonical), UNIQUE INDEX UNIQ_8D93D649C05FB297 (confirmation_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE disabled ADD CONSTRAINT FK_84FE6EE3CCFA12B8 FOREIGN KEY (profile_id) REFERENCES profile (id)');
        $this->addSql('ALTER TABLE disabled_deficiency ADD CONSTRAINT FK_8DD542876C9E327F FOREIGN KEY (disabled_id) REFERENCES disabled (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE disabled_deficiency ADD CONSTRAINT FK_8DD54287F4C36BA3 FOREIGN KEY (deficiency_id) REFERENCES deficiency (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE profile_gesture ADD CONSTRAINT FK_703E64ACCCFA12B8 FOREIGN KEY (profile_id) REFERENCES profile (id)');
        $this->addSql('ALTER TABLE profile_gesture ADD CONSTRAINT FK_703E64ACB1B8E511 FOREIGN KEY (gesture_id) REFERENCES gesture (id)');
        $this->addSql('ALTER TABLE gesture_tag ADD CONSTRAINT FK_25C4707B1B8E511 FOREIGN KEY (gesture_id) REFERENCES gesture (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE gesture_tag ADD CONSTRAINT FK_25C4707BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE disabled_deficiency DROP FOREIGN KEY FK_8DD54287F4C36BA3');
        $this->addSql('ALTER TABLE disabled_deficiency DROP FOREIGN KEY FK_8DD542876C9E327F');
        $this->addSql('ALTER TABLE disabled DROP FOREIGN KEY FK_84FE6EE3CCFA12B8');
        $this->addSql('ALTER TABLE profile_gesture DROP FOREIGN KEY FK_703E64ACCCFA12B8');
        $this->addSql('ALTER TABLE gesture_tag DROP FOREIGN KEY FK_25C4707BAD26311');
        $this->addSql('ALTER TABLE profile_gesture DROP FOREIGN KEY FK_703E64ACB1B8E511');
        $this->addSql('ALTER TABLE gesture_tag DROP FOREIGN KEY FK_25C4707B1B8E511');
        $this->addSql('DROP TABLE deficiency');
        $this->addSql('DROP TABLE disabled');
        $this->addSql('DROP TABLE disabled_deficiency');
        $this->addSql('DROP TABLE profile');
        $this->addSql('DROP TABLE profile_gesture');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE gesture');
        $this->addSql('DROP TABLE gesture_tag');
        $this->addSql('DROP TABLE `user`');
    }
}
