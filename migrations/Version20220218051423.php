<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220218051423 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE commentaire ADD CONSTRAINT FK_67F068BCA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_1483A5E96D861B89');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_1483A5E92D6BA2D9');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_1483A5E9CCF9E01E');
        $this->addSql('DROP INDEX idx_1483a5e9ccf9e01e ON user');
        $this->addSql('CREATE INDEX IDX_8D93D649CCF9E01E ON user (departement_id)');
        $this->addSql('DROP INDEX idx_1483a5e96d861b89 ON user');
        $this->addSql('CREATE INDEX IDX_8D93D6496D861B89 ON user (equipe_id)');
        $this->addSql('DROP INDEX idx_1483a5e92d6ba2d9 ON user');
        $this->addSql('CREATE INDEX IDX_8D93D6492D6BA2D9 ON user (reclamation_id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_1483A5E96D861B89 FOREIGN KEY (equipe_id) REFERENCES equipe (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_1483A5E92D6BA2D9 FOREIGN KEY (reclamation_id) REFERENCES reclamation (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_1483A5E9CCF9E01E FOREIGN KEY (departement_id) REFERENCES departement (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67DA76ED395');
        $this->addSql('ALTER TABLE commentaire DROP FOREIGN KEY FK_67F068BCA76ED395');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649CCF9E01E');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6496D861B89');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6492D6BA2D9');
        $this->addSql('DROP INDEX idx_8d93d649ccf9e01e ON user');
        $this->addSql('CREATE INDEX IDX_1483A5E9CCF9E01E ON user (departement_id)');
        $this->addSql('DROP INDEX idx_8d93d6496d861b89 ON user');
        $this->addSql('CREATE INDEX IDX_1483A5E96D861B89 ON user (equipe_id)');
        $this->addSql('DROP INDEX idx_8d93d6492d6ba2d9 ON user');
        $this->addSql('CREATE INDEX IDX_1483A5E92D6BA2D9 ON user (reclamation_id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649CCF9E01E FOREIGN KEY (departement_id) REFERENCES departement (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6496D861B89 FOREIGN KEY (equipe_id) REFERENCES equipe (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6492D6BA2D9 FOREIGN KEY (reclamation_id) REFERENCES reclamation (id)');
    }
}
