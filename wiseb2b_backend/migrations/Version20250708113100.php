<?php

declare(strict_types=1);

namespace App\Migrations; 

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20250708133000 extends AbstractMigration 
{
    public function getDescription(): string
    {
        return 'Adds a new WiseB2B supplier to the GpsrSupplier entity.';
    }

    public function up(Schema $schema): void
    {
        // This migration adds a new GpsrSupplier.
        // Assuming the table is named 'gpsr_supplier'.
        // Make sure the column names match your GpsrSupplier entity.
        // Address is often stored as JSON or in separate columns.
        // Below is an example for storing the address in separate columns.
        // If you store it as JSON, you need to adjust the query.

        // Example for columns: symbol, nip, phone_number, email, trader_name,
        // address_street, address_zip_code, address_city, address_country
        // Make sure the column names are consistent with your database.

        $this->addSql("
            INSERT INTO gpsr_supplier (symbol, nip, phone_number, email, trader_name, address_street, address_zip_code, address_city, address_country)
            VALUES (
                'WiseB2B',
                '1234567890',
                '000111999',
                'przykladowy_jan_kowalski@example.com',
                'WiseB2B Sp. z o.o.',
                'Przykładowa 44',
                '00-000',
                'Warszawa',
                'Polska'
            )
        ");
    }

    public function down(Schema $schema): void
    {
        // This migration removes the supplier added in the up() method.
        // It is important to be able to revert changes.
        $this->addSql("DELETE FROM gpsr_supplier WHERE symbol = 'WiseB2B'");
    }
}
