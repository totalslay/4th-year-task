<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251123180157 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE accrual (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(50) NOT NULL, amount DOUBLE PRECISION NOT NULL, employee_id INT NOT NULL, period_id INT NOT NULL, INDEX IDX_4BB15D7F8C03F15C (employee_id), INDEX IDX_4BB15D7FEC8B7ADE (period_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE adjustment (id INT AUTO_INCREMENT NOT NULL, changed_field_name VARCHAR(100) NOT NULL, old_value LONGTEXT NOT NULL, new_value LONGTEXT NOT NULL, change_reason LONGTEXT NOT NULL, changed_by VARCHAR(100) NOT NULL, changed_at DATETIME NOT NULL, employee_id INT NOT NULL, accrual_id INT DEFAULT NULL, deduction_id INT DEFAULT NULL, salary_calculation_id INT DEFAULT NULL, tax_rule_id INT DEFAULT NULL, INDEX IDX_89F978168C03F15C (employee_id), INDEX IDX_89F978168A9F4A15 (accrual_id), INDEX IDX_89F978162319F88E (deduction_id), INDEX IDX_89F97816AC6AF434 (salary_calculation_id), INDEX IDX_89F978163506A35B (tax_rule_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE deduction (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(50) NOT NULL, amount DOUBLE PRECISION NOT NULL, period_id INT NOT NULL, employee_id INT NOT NULL, INDEX IDX_6E3D6F93EC8B7ADE (period_id), INDEX IDX_6E3D6F938C03F15C (employee_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE employee (id INT AUTO_INCREMENT NOT NULL, full_name VARCHAR(255) NOT NULL, tin VARCHAR(12) NOT NULL, bank_account VARCHAR(20) NOT NULL, employment_type VARCHAR(20) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE payroll_period (id INT AUTO_INCREMENT NOT NULL, start_date DATE NOT NULL, end_date DATE NOT NULL, status VARCHAR(20) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE payslip (id INT AUTO_INCREMENT NOT NULL, pdf_filename VARCHAR(255) NOT NULL, generated_at DATETIME NOT NULL, employee_id INT NOT NULL, INDEX IDX_9A13CDF08C03F15C (employee_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE salary_calculation (id INT AUTO_INCREMENT NOT NULL, gross_amount DOUBLE PRECISION NOT NULL, net_amount DOUBLE PRECISION NOT NULL, period_id INT NOT NULL, UNIQUE INDEX UNIQ_30BFC60BEC8B7ADE (period_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE tax_rule (id INT AUTO_INCREMENT NOT NULL, min_amount DOUBLE PRECISION NOT NULL, max_amount DOUBLE PRECISION DEFAULT NULL, rate DOUBLE PRECISION NOT NULL, tax_type VARCHAR(50) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE accrual ADD CONSTRAINT FK_4BB15D7F8C03F15C FOREIGN KEY (employee_id) REFERENCES employee (id)');
        $this->addSql('ALTER TABLE accrual ADD CONSTRAINT FK_4BB15D7FEC8B7ADE FOREIGN KEY (period_id) REFERENCES payroll_period (id)');
        $this->addSql('ALTER TABLE adjustment ADD CONSTRAINT FK_89F978168C03F15C FOREIGN KEY (employee_id) REFERENCES employee (id)');
        $this->addSql('ALTER TABLE adjustment ADD CONSTRAINT FK_89F978168A9F4A15 FOREIGN KEY (accrual_id) REFERENCES accrual (id)');
        $this->addSql('ALTER TABLE adjustment ADD CONSTRAINT FK_89F978162319F88E FOREIGN KEY (deduction_id) REFERENCES deduction (id)');
        $this->addSql('ALTER TABLE adjustment ADD CONSTRAINT FK_89F97816AC6AF434 FOREIGN KEY (salary_calculation_id) REFERENCES salary_calculation (id)');
        $this->addSql('ALTER TABLE adjustment ADD CONSTRAINT FK_89F978163506A35B FOREIGN KEY (tax_rule_id) REFERENCES tax_rule (id)');
        $this->addSql('ALTER TABLE deduction ADD CONSTRAINT FK_6E3D6F93EC8B7ADE FOREIGN KEY (period_id) REFERENCES payroll_period (id)');
        $this->addSql('ALTER TABLE deduction ADD CONSTRAINT FK_6E3D6F938C03F15C FOREIGN KEY (employee_id) REFERENCES employee (id)');
        $this->addSql('ALTER TABLE payslip ADD CONSTRAINT FK_9A13CDF08C03F15C FOREIGN KEY (employee_id) REFERENCES employee (id)');
        $this->addSql('ALTER TABLE salary_calculation ADD CONSTRAINT FK_30BFC60BEC8B7ADE FOREIGN KEY (period_id) REFERENCES payroll_period (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE accrual DROP FOREIGN KEY FK_4BB15D7F8C03F15C');
        $this->addSql('ALTER TABLE accrual DROP FOREIGN KEY FK_4BB15D7FEC8B7ADE');
        $this->addSql('ALTER TABLE adjustment DROP FOREIGN KEY FK_89F978168C03F15C');
        $this->addSql('ALTER TABLE adjustment DROP FOREIGN KEY FK_89F978168A9F4A15');
        $this->addSql('ALTER TABLE adjustment DROP FOREIGN KEY FK_89F978162319F88E');
        $this->addSql('ALTER TABLE adjustment DROP FOREIGN KEY FK_89F97816AC6AF434');
        $this->addSql('ALTER TABLE adjustment DROP FOREIGN KEY FK_89F978163506A35B');
        $this->addSql('ALTER TABLE deduction DROP FOREIGN KEY FK_6E3D6F93EC8B7ADE');
        $this->addSql('ALTER TABLE deduction DROP FOREIGN KEY FK_6E3D6F938C03F15C');
        $this->addSql('ALTER TABLE payslip DROP FOREIGN KEY FK_9A13CDF08C03F15C');
        $this->addSql('ALTER TABLE salary_calculation DROP FOREIGN KEY FK_30BFC60BEC8B7ADE');
        $this->addSql('DROP TABLE accrual');
        $this->addSql('DROP TABLE adjustment');
        $this->addSql('DROP TABLE deduction');
        $this->addSql('DROP TABLE employee');
        $this->addSql('DROP TABLE payroll_period');
        $this->addSql('DROP TABLE payslip');
        $this->addSql('DROP TABLE salary_calculation');
        $this->addSql('DROP TABLE tax_rule');
    }
}
