const TronWeb = require('tronweb');

// ✅ Contrato USDT oficial na rede Shasta
const contractAddress = 'TG3XXyExBkPp9nzdajDZsozEu4BkaSJozs';

// Parâmetros recebidos
const privateKey = process.argv[2];
const toAddress = process.argv[3];
const amount = process.argv[4];

const tronWeb = new TronWeb({
    fullHost: 'https://api.shasta.trongrid.io',
    privateKey: privateKey
});

(async () => {
    try {
        console.log("🔄 Iniciando envio de USDT...");
        const contract = await tronWeb.contract().at(contractAddress);
        console.log("✅ Contrato carregado com sucesso.");

        const usdtAmount = tronWeb.toSun(amount);
        console.log(`🚀 Enviando ${amount} USDT (${usdtAmount} sun) para ${toAddress}`);

        // Envia a transação
        const tx = await contract.transfer(toAddress, usdtAmount).send({
            feeLimit: 100_000_000
        });

        console.log("✅ Transação enviada:", tx);

        console.log(JSON.stringify({
            success: true,
            tx_id: tx
        }));
    } catch (error) {
        console.error('❌ Erro no envio:', error);

        const errMsg = error?.message || error?.toString() || 'Erro desconhecido';
        console.log(JSON.stringify({
            success: false,
            error: errMsg
        }));
    }
})();
