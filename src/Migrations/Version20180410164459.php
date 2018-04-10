<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180410164459 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE tag_gesture');
        $this->addSql('ALTER TABLE gesture ADD is_published TINYINT(1) NOT NULL, ADD publication_date DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE tag_gesture (tag_id INT NOT NULL, gesture_id INT NOT NULL, INDEX IDX_91B1979ABAD26311 (tag_id), INDEX IDX_91B1979AB1B8E511 (gesture_id), PRIMARY KEY(tag_id, gesture_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tag_gesture ADD CONSTRAINT FK_91B1979AB1B8E511 FOREIGN KEY (gesture_id) REFERENCES gesture (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tag_gesture ADD CONSTRAINT FK_91B1979ABAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE gesture DROP is_published, DROP publication_date');
    }
}
