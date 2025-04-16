// tronweb/send_usdt.js
const TronWeb = require('tronweb');

// Configuração para Shasta
const tronWeb = new TronWeb({
    fullHost: 'https://api.shasta.trongrid.io',
    privateKey: process.argv[2], // chave privada da carteira de origem
});

const USDT_CONTRACT = 'TXYZopYRdj2D9XRtbG411XZZ3kM5VkAeBf'; // contrato USDT TRC20 em Shasta

async function sendUSDT(to, amount) {
    try {
        const contract = await tronWeb.contract().at(USDT_CONTRACT);

        const tx = await contract.transfer(to, amount * 1_000_000).send();

        console.log(JSON.stringify({ success: true, tx_id: tx }));
    } catch (error) {
        console.error(JSON.stringify({ success: false, error: error.message }));
    }
}

sendUSDT(process.argv[3], parseFloat(process.argv[4]));
