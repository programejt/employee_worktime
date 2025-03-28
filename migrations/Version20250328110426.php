<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250328110426 extends AbstractMigration
{
  public function up(Schema $schema): void
  {
    $this->addSql(<<<'SQL'
            CREATE TABLE employee (id BINARY(16) NOT NULL COMMENT '(DC2Type:uuid)', name VARCHAR(60) NOT NULL, surname VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
    $this->addSql(<<<'SQL'
            CREATE TABLE worktime (id INT AUTO_INCREMENT NOT NULL, employee_id BINARY(16) NOT NULL COMMENT '(DC2Type:uuid)', start_date DATETIME NOT NULL, end_date DATETIME NOT NULL, start_day DATE NOT NULL, INDEX IDX_5891D6238C03F15C (employee_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
    $this->addSql(<<<'SQL'
            ALTER TABLE worktime ADD CONSTRAINT FK_5891D6238C03F15C FOREIGN KEY (employee_id) REFERENCES employee (id)
        SQL);
  }

  public function down(Schema $schema): void
  {
    $this->addSql(<<<'SQL'
            ALTER TABLE worktime DROP FOREIGN KEY FK_5891D6238C03F15C
        SQL);
    $this->addSql(<<<'SQL'
            DROP TABLE employee
        SQL);
    $this->addSql(<<<'SQL'
            DROP TABLE worktime
        SQL);
  }
}
