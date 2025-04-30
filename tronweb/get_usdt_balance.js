const TronWeb = require('tronweb');

const fullNode = 'https://api.shasta.trongrid.io';
const solidityNode = fullNode;
const eventServer = fullNode;

// Chave privada dummy só para leitura (nunca usada para enviar TRX)
const privateKey = 'd1f5f4c3219d3e65dbb2676f8e72fd77972e5f95eb25d3b0b41f7aeee0eeaa4a';

const tronWeb = new TronWeb(fullNode, solidityNode, eventServer, privateKey);

async function getUSDTBalance(address) {
    if (!address) {
        console.error('Endereço não informado.');
        return;
    }

    const usdtContract = await tronWeb.contract().at('TG3XXyExBkPp9nzdajDZsozEu4BkaSJozs');
    const balance = await usdtContract.balanceOf(address).call();
    console.log(JSON.stringify({ success: true, balance: Number(balance) / 1e6 }));
}

const address = process.argv[2];
getUSDTBalance(address).catch(e => console.error(e));
