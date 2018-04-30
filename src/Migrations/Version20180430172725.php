<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180430172725 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE deficiency (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, type VARCHAR(50) DEFAULT NULL, severity VARCHAR(50) DEFAULT NULL, UNIQUE INDEX UNIQ_1622D0B25E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE disabled (id INT AUTO_INCREMENT NOT NULL, firstname VARCHAR(50) NOT NULL, lastname VARCHAR(50) NOT NULL, birthday DATE DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE disabled_deficiency (disabled_id INT NOT NULL, deficiency_id INT NOT NULL, INDEX IDX_8DD542876C9E327F (disabled_id), INDEX IDX_8DD54287F4C36BA3 (deficiency_id), PRIMARY KEY(disabled_id, deficiency_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE profile (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, content VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_8157AA0F7E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE profile_gesture (profile_id INT NOT NULL, gesture_id INT NOT NULL, INDEX IDX_703E64ACCCFA12B8 (profile_id), INDEX IDX_703E64ACB1B8E511 (gesture_id), PRIMARY KEY(profile_id, gesture_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE disabled_deficiency ADD CONSTRAINT FK_8DD542876C9E327F FOREIGN KEY (disabled_id) REFERENCES disabled (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE disabled_deficiency ADD CONSTRAINT FK_8DD54287F4C36BA3 FOREIGN KEY (deficiency_id) REFERENCES deficiency (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE profile ADD CONSTRAINT FK_8157AA0F7E3C61F9 FOREIGN KEY (owner_id) REFERENCES disabled (id)');
        $this->addSql('ALTER TABLE profile_gesture ADD CONSTRAINT FK_703E64ACCCFA12B8 FOREIGN KEY (profile_id) REFERENCES profile (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE profile_gesture ADD CONSTRAINT FK_703E64ACB1B8E511 FOREIGN KEY (gesture_id) REFERENCES gesture (id) ON DELETE CASCADE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A380BA305E237E06 ON gesture (name)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE disabled_deficiency DROP FOREIGN KEY FK_8DD54287F4C36BA3');
        $this->addSql('ALTER TABLE disabled_deficiency DROP FOREIGN KEY FK_8DD542876C9E327F');
        $this->addSql('ALTER TABLE profile DROP FOREIGN KEY FK_8157AA0F7E3C61F9');
        $this->addSql('ALTER TABLE profile_gesture DROP FOREIGN KEY FK_703E64ACCCFA12B8');
        $this->addSql('DROP TABLE deficiency');
        $this->addSql('DROP TABLE disabled');
        $this->addSql('DROP TABLE disabled_deficiency');
        $this->addSql('DROP TABLE profile');
        $this->addSql('DROP TABLE profile_gesture');
        $this->addSql('DROP INDEX UNIQ_A380BA305E237E06 ON gesture');
    }
}
