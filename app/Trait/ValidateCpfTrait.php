<?php

declare(strict_types=1);

namespace App\Trait;

use Closure;

trait ValidateCpfTrait
{
    /**
     * Obtém regra de validação para CPF válido.
     */
    public static function getCpfValidationRule(): Closure
    {
        return function ($attribute, $value, $fail): void {
            // Remove caracteres não numéricos
            $cpf = preg_replace('/[^0-9]/', '', $value);

            // Verifica se o CPF tem 11 dígitos
            if (strlen($cpf) != 11) {
                $fail('CPF não possui 11 digitos.');

                return;
            }

            // Verifica CPFs com dígitos repetidos
            if (preg_match('/(\d)\1{10}/', $cpf)) {
                $fail('CPF com digitos repetidos.');

                return;
            }

            // Calcula o primeiro dígito verificador
            $sum = 0;

            for ($i = 0; $i < 9; $i++) {
                $sum += (int) $cpf[$i] * (10 - $i);
            }
            $remainder = $sum % 11;
            $digit1 = $remainder < 2 ? 0 : 11 - $remainder;

            // Calcula o segundo dígito verificador
            $sum = 0;

            for ($i = 0; $i < 9; $i++) {
                $sum += (int) $cpf[$i] * (11 - $i);
            }
            $sum += $digit1 * 2;
            $remainder = $sum % 11;
            $digit2 = $remainder < 2 ? 0 : 11 - $remainder;

            // Verifica se os dígitos verificadores estão corretos
            if ($cpf[9] != $digit1 || $cpf[10] != $digit2) {
                $fail('CPF não é valido.');
            }
        };
    }
}
