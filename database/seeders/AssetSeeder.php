<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Asset;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Carbon;

class AssetSeeder extends Seeder
{
    public function run(): void
    {
        $filePath = storage_path('app/public/cleaned_assets.xlsx');

        if (!file_exists($filePath)) {
            $this->command->error("Excel file not found at: $filePath");
            return;
        }

        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        unset($rows[1]); // Remove header row

        foreach ($rows as $row) {
            $propertyNumber = trim($row['A'] ?? '');
            if (empty($propertyNumber)) {
                continue;
            }

            $acquiredYear = (int) trim($row['J'] ?? '');
            $acquiredYear = ($acquiredYear >= 1900 && $acquiredYear <= Carbon::now()->year) ? $acquiredYear : null;

            Asset::updateOrCreate(
                ['property_number' => $propertyNumber],
                [
                    'brand' => trim($row['B'] ?? ''),
                    'model' => trim($row['C'] ?? ''),
                    'type' => trim($row['D'] ?? 'Unknown'),
                    'condition' => $this->sanitizeCondition($row['E'] ?? 'Good'),
                    'location' => trim($row['F'] ?? ''),
                    'building_name' => trim($row['G'] ?? ''),
                    'fund' => trim($row['H'] ?? ''),
                    'price' => is_numeric($row['I']) ? round($row['I'], 2) : null,
                    'acquired_at' => $acquiredYear,
                    'previous_custodian' => trim($row['K'] ?? ''),
                    'custodian' => trim($row['L'] ?? ''),
                    'latitude' => is_numeric($row['M']) ? floatval($row['M']) : null,
                    'longitude' => is_numeric($row['N']) ? floatval($row['N']) : null,
                ]
            );
        }

        $this->command->info("Assets seeded successfully including coordinates.");
    }

    private function sanitizeCondition($value): string
    {
        $allowed = ['Good', 'Fair', 'Poor'];
        $value = ucfirst(strtolower(trim($value)));

        return in_array($value, $allowed) ? $value : 'Good';
    }
}
