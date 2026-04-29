<?php

namespace App\Domains\OfficialDocument\Services;

use Illuminate\Support\Facades\DB;
use App\Domains\OfficialDocument\Models\OfficialDocument;
use App\Domains\OfficialDocument\Models\OfficialDocumentCategory;

class NumberingService
{
    /**
     * Retorna o próximo número disponível para uma categoria e ano,
     * garantindo integridade e prevenindo duplicidades em acessos simultâneos.
     */
    public function getNextNumber(int $unitId, int $categoryId, int $year): int
    {
        return DB::transaction(function () use ($unitId, $categoryId, $year) {
            // lockForUpdate impede que outra transação leia a mesma linha antes desta finalizar
            $lastDoc = OfficialDocument::where('unit_id', $unitId)
                ->where('category_id', $categoryId)
                ->where('year', $year)
                ->lockForUpdate()
                ->orderBy('number', 'desc')
                ->first();

            return $lastDoc ? $lastDoc->number + 1 : 1;
        });
    }

    /**
     * Monta o "Full Number" formatado com zero à esquerda (Ex: 004/2026/OF)
     */
    public function formatFullNumber(int $number, int $year, int $categoryId): string
    {
        $category = OfficialDocumentCategory::find($categoryId);
        $acronym = $category ? $category->acronym : 'DOC';

        // Formata com 3 casas (ex: 001, 045, 120)
        $formattedNumber = str_pad($number, 3, '0', STR_PAD_LEFT);

        return "{$formattedNumber}/{$year}/{$acronym}";
    }
}
