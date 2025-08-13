<?php

declare(strict_types=1);

namespace App\Trait;

use Closure;

trait ValidateCnpjTrait
{
    /**
     * Obtém regra de validação para CNPJ válido.
     */
    public static function getCnpjValidationRule(): Closure
    {
        return function ($attribute, $value, $fail): void {
            // Remove caracteres não numéricos
            $cnpj = preg_replace('/[^0-9]/', '', $value);

            // Verifica se o CNPJ tem 14 dígitos
            if (strlen($cnpj) != 14) {
                $fail('CNPJ não possui 14 digitos.');

                return;
            }

            // Verifica CNPJs com dígitos repetidos
            if (preg_match('/(\d)\1{13}/', $cnpj)) {
                $fail('CNPJ com digitos repetidos.');

                return;
            }

            // Calcula o primeiro dígito verificador
            $sum = 0;
            $weights = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];

            for ($i = 0; $i < 12; $i++) {
                $sum += (int) $cnpj[$i] * $weights[$i];
            }

            $remainder = $sum % 11;
            $digit1 = $remainder < 2 ? 0 : 11 - $remainder;

            // Calcula o segundo dígito verificador
            $sum = 0;
            $weights = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];

            for ($i = 0; $i < 12; $i++) {
                $sum += (int) $cnpj[$i] * $weights[$i];
            }
            $sum += $digit1 * $weights[12];

            $remainder = $sum % 11;
            $digit2 = $remainder < 2 ? 0 : 11 - $remainder;

            // Verifica se os dígitos verificadores estão corretos
            if ($cnpj[12] != $digit1 || $cnpj[13] != $digit2) {
                $fail('CNPJ não é válido.');
            }
        };
    }
}
