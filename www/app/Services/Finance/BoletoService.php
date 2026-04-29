<?php

namespace App\Services\Finance;

use App\Domains\Finance\Models\BankAccount;
use App\Domains\Finance\Models\Invoice;
use App\Domains\Shared\Models\UnitSetting;
use OpenBoleto\Agente;
use OpenBoleto\BoletoAbstract;
use OpenBoleto\Banco\BancoDoBrasil;
use OpenBoleto\Banco\Bradesco;
use OpenBoleto\Banco\Caixa;
use OpenBoleto\Banco\Itau;
use OpenBoleto\Banco\Santander;

class BoletoService
{
    /**
     * Mapa de códigos bancários para as classes do OpenBoleto.
     * Esta abstração permite registrar novos bancos dinamicamente.
     */
    protected array $bankClassMap = [
        '001' => BancoDoBrasil::class,
        '237' => Bradesco::class,
        '104' => Caixa::class,
        '341' => Itau::class,
        '033' => Santander::class,
    ];

    /**
     * Adiciona suporte a um novo banco customizado.
     */
    public function registerBank(string $code, string $className): void
    {
        if (is_subclass_of($className, BoletoAbstract::class)) {
            $this->bankClassMap[$code] = $className;
        } else {
            throw new \InvalidArgumentException("A classe do banco deve estender BoletoAbstract.");
        }
    }

    /**
     * Gera o objeto BoletoAbstract a partir de um Invoice.
     */
    public function generateBoleto(Invoice $invoice): BoletoAbstract
    {
        $invoice->loadMissing(['student.guardians', 'bankAccount', 'unit']);
        $bankAccount = $invoice->bankAccount;
        
        if (!$bankAccount) {
            throw new \Exception("Nenhuma conta bancária vinculada à fatura ID {$invoice->id}.");
        }

        $student = $invoice->student;
        $unit = $invoice->unit;

        // Recupera responsável financeiro (pega o primeiro por padrão)
        $guardian = $student->guardians->first();
        if (!$guardian) {
            throw new \Exception("Aluno não possui responsável cadastrado para emissão de boleto.");
        }

        $sacado = new Agente(
            $guardian->name, 
            $guardian->cpf ?? $guardian->document ?? '000.000.000-00', 
            $guardian->address ?? 'Endereço não cadastrado', 
            $guardian->zipcode ?? '00000-000', 
            $guardian->city ?? 'Cidade', 
            $guardian->state ?? 'UF'
        );

        $cedente = new Agente(
            $unit->name, 
            $unit->cnpj ?? '00.000.000/0000-00', 
            $unit->address ?? 'Endereço da Escola', 
            $unit->zipcode ?? '00000-000', 
            $unit->city ?? 'Cidade', 
            $unit->state ?? 'UF'
        );

        $bankClass = $this->bankClassMap[$bankAccount->bank_code] ?? null;

        if (!$bankClass) {
            throw new \Exception("Banco de código {$bankAccount->bank_code} não suportado no BoletoService.");
        }

        // Calcula multas e juros para enviar nas instruções
        $multa = $bankAccount->fine_percentage ? "Cobrar multa de {$bankAccount->fine_percentage}% após o vencimento." : "";
        $juros = $bankAccount->interest_percentage ? "Cobrar juros de {$bankAccount->interest_percentage}% ao mês após vencimento." : "";

        // Instruções com chave Pix
        $instrucoes = [
            "- Não receber após 30 dias do vencimento.",
            $multa,
            $juros,
            $bankAccount->instruction_lines,
        ];

        if ($invoice->pix_key) {
            $instrucoes[] = "PAGUE VIA PIX: Chave " . $invoice->pix_key;
        }

        $instrucoes = array_filter($instrucoes); // Limpa vazios

        // Geração do Boleto
        $boleto = new $bankClass([
            'dataVencimento' => clone $invoice->due_date,
            'valor' => (float) $invoice->amount,
            'sequencial' => $invoice->id, // Nosso número / Sequencial
            'sacado' => $sacado,
            'cedente' => $cedente,
            'agencia' => $bankAccount->agency, // Até 4 dígitos
            'carteira' => $bankAccount->wallet ?? '109',
            'conta' => $bankAccount->account, // Até 8 dígitos
            'contaDv' => '0', // TODO: Abstrair DV se necessário
            'descricaoDemonstrativo' => [
                'Mensalidade Escolar - Parcela ' . ($invoice->installment_number ?? 1),
                'Aluno(a): ' . $student->name,
                'Referência: ' . $invoice->due_date->format('m/Y'),
            ],
            'instrucoes' => $instrucoes,
        ]);

        return $boleto;
    }
}
