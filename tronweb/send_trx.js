const TronWeb = require('tronweb');

const privateKey = process.argv[2];
const toAddress = process.argv[3];
const amount = process.argv[4];

const tronWeb = new TronWeb({
    fullHost: 'https://api.shasta.trongrid.io',
    privateKey: privateKey
});

(async () => {
    try {
        const tx = await tronWeb.transactionBuilder.sendTrx(toAddress, tronWeb.toSun(amount));
        const signedTx = await tronWeb.trx.sign(tx);
        const receipt = await tronWeb.trx.sendRawTransaction(signedTx);

        console.log(JSON.stringify({
            success: true,
            tx_id: receipt.txid
        }));
    } catch (error) {
        console.error(error);
        console.log(JSON.stringify({
            success: false,
            error: error.message
        }));
    }
})();
