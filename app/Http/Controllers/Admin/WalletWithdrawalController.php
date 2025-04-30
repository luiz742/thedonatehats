<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Wallet;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class WalletWithdrawalController extends Controller
{

    public function index()
    {
        // Obtenha as carteiras com informações do usuário
        $wallets = Wallet::with('user')->get();

        // Filtra as carteiras com saldo maior que 0, se necessário
        $walletsWithBalance = $wallets->filter(fn($wallet) => $wallet->balance > 0)->values();

        return inertia('Admin/WalletWithdrawals/Index', [
            'wallets' => $walletsWithBalance,
        ]);
    }

    public function withdraw(Request $request, Wallet $wallet)
    {
        // Valida a quantidade de USDT a ser sacada
        $request->validate([
            'amount' => 'required|numeric|min:0.000001',
        ]);

        // Recupera o endereço fixo de destino para o saque
        $toAddress = config('tron.address'); // Endereço fixo de destino
        $amount = $request->amount;

        // Verifica se o endereço de destino está configurado corretamente
        if (empty($toAddress)) {
            Log::error('Endereço de destino não configurado', ['toAddress' => $toAddress]);
            return back()->withErrors(['Erro: Endereço de destino não configurado corretamente.']);
        }

        try {
            // Descriptografa a chave privada
            $privateKey = Crypt::decryptString($wallet->private_key);
        } catch (\Exception $e) {
            Log::error('Erro ao descriptografar a chave privada', ['exception' => $e->getMessage()]);
            return back()->withErrors(['Erro ao descriptografar a chave privada.']);
        }

        // Define o caminho do script Node.js
        $scriptPath = base_path('tronweb/send_usdt.js');

        // Monta o comando para execução do script
        $command = sprintf(
            'node %s %s %s %s',
            escapeshellarg($scriptPath),
            escapeshellarg($privateKey),
            escapeshellarg($toAddress),
            escapeshellarg($amount)
        );

        // Log do comando que será executado
        Log::info('Comando executado', ['command' => $command]);

        // Executa o script Node.js
        $output = shell_exec($command);

        // Log da saída do script
        Log::info('Saída do script', ['output' => $output]);

        // Decodifica a resposta do script
        $response = json_decode($output, true);

        // Verifica se a resposta é válida e se contém a chave 'success'
        if (!$response || !isset($response['success'])) {
            // Adiciona um log para ajudar na depuração caso a resposta esteja vazia ou inválida
            Log::error('Resposta inválida do script de saque', ['output' => $output]);

            return back()->withErrors(['Erro ao executar o script de saque.']);
        }

        // Verifica se a chave 'success' é verdadeira e lida com erro se existir
        if (!$response['success']) {
            // Se existir um erro na resposta, mostre a mensagem, senão uma mensagem padrão
            $errorMessage = isset($response['error']) ? $response['error'] : 'Erro desconhecido';

            Log::error('Erro no saque', ['error' => $errorMessage]);

            return back()->withErrors(['Erro no saque: ' . $errorMessage]);
        }

        // Caso o saque tenha sido bem-sucedido, exibe a transação realizada
        return back()->with('success', 'Saque realizado com sucesso. TX: ' . $response['tx_id']);
    }





    public function fundWallet(Request $request, Wallet $wallet)
{
    set_time_limit(120); // Evita erro de tempo

    $request->validate([
        'amount' => 'required|numeric|min:1'
    ]);

    // Use config() em vez de env()
    $fromPrivateKey = config('tron.private_key');
    $toAddress = $wallet->address;
    $amount = $request->amount;

    // Caminho do script JS
    $scriptPath = base_path('tronweb/send_trx.js');

    // Agora tenta executar sem especificar o caminho completo do Node
    $command = sprintf(
        'node %s %s %s %s 2>&1', // Redireciona stderr para stdout
        escapeshellarg($scriptPath),
        escapeshellarg($fromPrivateKey),
        escapeshellarg($toAddress),
        escapeshellarg($amount)
    );

    Log::info('Comando executado', ['command' => $command]);

    // Executa o script
    $output = shell_exec($command);
    Log::info('Output do script', ['output' => $output]);

    $response = json_decode($output, true);

    if (!$response || !isset($response['success'])) {
        return response()->json([
            'success' => false,
            'message' => 'Resposta inválida do script.'
        ], 500);
    }

    if (!$response['success']) {
        return response()->json([
            'success' => false,
            'message' => 'Erro no envio de TRX: ' . ($response['error'] ?? 'Erro desconhecido')
        ], 500);
    }

    return response()->json([
        'success' => true,
        'tx_id' => $response['tx_id'],
        'message' => 'TRX enviados com sucesso.'
    ]);
}


}

