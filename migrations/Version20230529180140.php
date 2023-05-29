<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230529180140 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE card (id BIGINT AUTO_INCREMENT NOT NULL, created_by_id BIGINT DEFAULT NULL, type VARCHAR(255) NOT NULL, text JSON NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_161498D3B03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE card_deck (card_id BIGINT NOT NULL, deck_id BIGINT NOT NULL, INDEX IDX_A39F34954ACC9A20 (card_id), INDEX IDX_A39F3495111948DC (deck_id), PRIMARY KEY(card_id, deck_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE deck (id BIGINT AUTO_INCREMENT NOT NULL, created_by_id BIGINT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, public TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_4FAC3637B03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game (id BIGINT AUTO_INCREMENT NOT NULL, current_round_id BIGINT DEFAULT NULL, deck_id BIGINT DEFAULT NULL, created_by_id BIGINT DEFAULT NULL, slug VARCHAR(255) NOT NULL, number_of_rounds INT NOT NULL, status VARCHAR(255) NOT NULL, spectatable TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_232B318C3B78268A (current_round_id), INDEX IDX_232B318C111948DC (deck_id), INDEX IDX_232B318CB03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE message (id BIGINT AUTO_INCREMENT NOT NULL, created_by_id BIGINT DEFAULT NULL, game_id BIGINT DEFAULT NULL, message VARCHAR(255) NOT NULL, deleted_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, INDEX IDX_B6BD307FB03A8386 (created_by_id), INDEX IDX_B6BD307FE48FD905 (game_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE play (id BIGINT AUTO_INCREMENT NOT NULL, player_id BIGINT DEFAULT NULL, turn_id BIGINT DEFAULT NULL, points INT DEFAULT NULL, likes INT NOT NULL, featured TINYINT(1) NOT NULL, deleted_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, INDEX IDX_5E89DEBA99E6F5DF (player_id), INDEX IDX_5E89DEBA1F4F9889 (turn_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE play_card (play_id BIGINT NOT NULL, card_id BIGINT NOT NULL, INDEX IDX_1D11660225576DBD (play_id), INDEX IDX_1D1166024ACC9A20 (card_id), PRIMARY KEY(play_id, card_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE player (id BIGINT AUTO_INCREMENT NOT NULL, user_id BIGINT DEFAULT NULL, game_id BIGINT DEFAULT NULL, ready TINYINT(1) NOT NULL, votes INT NOT NULL, voted TINYINT(1) NOT NULL, deleted_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, INDEX IDX_98197A65A76ED395 (user_id), INDEX IDX_98197A65E48FD905 (game_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE player_card (player_id BIGINT NOT NULL, card_id BIGINT NOT NULL, INDEX IDX_B40EC8E099E6F5DF (player_id), INDEX IDX_B40EC8E04ACC9A20 (card_id), PRIMARY KEY(player_id, card_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE round (id BIGINT AUTO_INCREMENT NOT NULL, current_turn_id BIGINT DEFAULT NULL, game_id BIGINT DEFAULT NULL, number INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_C5EEEA34B5421D0C (current_turn_id), INDEX IDX_C5EEEA34E48FD905 (game_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE turn (id BIGINT AUTO_INCREMENT NOT NULL, player_id BIGINT DEFAULT NULL, card_id BIGINT DEFAULT NULL, round_id BIGINT DEFAULT NULL, winning_play_id BIGINT DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, INDEX IDX_2020154799E6F5DF (player_id), INDEX IDX_202015474ACC9A20 (card_id), INDEX IDX_20201547A6005CA0 (round_id), UNIQUE INDEX UNIQ_20201547B5FBEC7A (winning_play_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id BIGINT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, internal_id VARCHAR(255) DEFAULT NULL, remember_token VARCHAR(255) DEFAULT NULL, nickname VARCHAR(255) DEFAULT NULL, password VARCHAR(255) DEFAULT NULL, activated TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE card ADD CONSTRAINT FK_161498D3B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE card_deck ADD CONSTRAINT FK_A39F34954ACC9A20 FOREIGN KEY (card_id) REFERENCES card (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE card_deck ADD CONSTRAINT FK_A39F3495111948DC FOREIGN KEY (deck_id) REFERENCES deck (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE deck ADD CONSTRAINT FK_4FAC3637B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318C3B78268A FOREIGN KEY (current_round_id) REFERENCES round (id)');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318C111948DC FOREIGN KEY (deck_id) REFERENCES deck (id)');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318CB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FE48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE play ADD CONSTRAINT FK_5E89DEBA99E6F5DF FOREIGN KEY (player_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE play ADD CONSTRAINT FK_5E89DEBA1F4F9889 FOREIGN KEY (turn_id) REFERENCES turn (id)');
        $this->addSql('ALTER TABLE play_card ADD CONSTRAINT FK_1D11660225576DBD FOREIGN KEY (play_id) REFERENCES play (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE play_card ADD CONSTRAINT FK_1D1166024ACC9A20 FOREIGN KEY (card_id) REFERENCES card (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE player ADD CONSTRAINT FK_98197A65A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE player ADD CONSTRAINT FK_98197A65E48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE player_card ADD CONSTRAINT FK_B40EC8E099E6F5DF FOREIGN KEY (player_id) REFERENCES player (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE player_card ADD CONSTRAINT FK_B40EC8E04ACC9A20 FOREIGN KEY (card_id) REFERENCES card (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE round ADD CONSTRAINT FK_C5EEEA34B5421D0C FOREIGN KEY (current_turn_id) REFERENCES turn (id)');
        $this->addSql('ALTER TABLE round ADD CONSTRAINT FK_C5EEEA34E48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE turn ADD CONSTRAINT FK_2020154799E6F5DF FOREIGN KEY (player_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE turn ADD CONSTRAINT FK_202015474ACC9A20 FOREIGN KEY (card_id) REFERENCES card (id)');
        $this->addSql('ALTER TABLE turn ADD CONSTRAINT FK_20201547A6005CA0 FOREIGN KEY (round_id) REFERENCES round (id)');
        $this->addSql('ALTER TABLE turn ADD CONSTRAINT FK_20201547B5FBEC7A FOREIGN KEY (winning_play_id) REFERENCES play (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE card DROP FOREIGN KEY FK_161498D3B03A8386');
        $this->addSql('ALTER TABLE card_deck DROP FOREIGN KEY FK_A39F34954ACC9A20');
        $this->addSql('ALTER TABLE card_deck DROP FOREIGN KEY FK_A39F3495111948DC');
        $this->addSql('ALTER TABLE deck DROP FOREIGN KEY FK_4FAC3637B03A8386');
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318C3B78268A');
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318C111948DC');
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318CB03A8386');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FB03A8386');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FE48FD905');
        $this->addSql('ALTER TABLE play DROP FOREIGN KEY FK_5E89DEBA99E6F5DF');
        $this->addSql('ALTER TABLE play DROP FOREIGN KEY FK_5E89DEBA1F4F9889');
        $this->addSql('ALTER TABLE play_card DROP FOREIGN KEY FK_1D11660225576DBD');
        $this->addSql('ALTER TABLE play_card DROP FOREIGN KEY FK_1D1166024ACC9A20');
        $this->addSql('ALTER TABLE player DROP FOREIGN KEY FK_98197A65A76ED395');
        $this->addSql('ALTER TABLE player DROP FOREIGN KEY FK_98197A65E48FD905');
        $this->addSql('ALTER TABLE player_card DROP FOREIGN KEY FK_B40EC8E099E6F5DF');
        $this->addSql('ALTER TABLE player_card DROP FOREIGN KEY FK_B40EC8E04ACC9A20');
        $this->addSql('ALTER TABLE round DROP FOREIGN KEY FK_C5EEEA34B5421D0C');
        $this->addSql('ALTER TABLE round DROP FOREIGN KEY FK_C5EEEA34E48FD905');
        $this->addSql('ALTER TABLE turn DROP FOREIGN KEY FK_2020154799E6F5DF');
        $this->addSql('ALTER TABLE turn DROP FOREIGN KEY FK_202015474ACC9A20');
        $this->addSql('ALTER TABLE turn DROP FOREIGN KEY FK_20201547A6005CA0');
        $this->addSql('ALTER TABLE turn DROP FOREIGN KEY FK_20201547B5FBEC7A');
        $this->addSql('DROP TABLE card');
        $this->addSql('DROP TABLE card_deck');
        $this->addSql('DROP TABLE deck');
        $this->addSql('DROP TABLE game');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE play');
        $this->addSql('DROP TABLE play_card');
        $this->addSql('DROP TABLE player');
        $this->addSql('DROP TABLE player_card');
        $this->addSql('DROP TABLE round');
        $this->addSql('DROP TABLE turn');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
