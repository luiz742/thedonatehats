// tronweb/generate_wallet.js
const TronWeb = require('tronweb');

const tronWeb = new TronWeb({
  fullHost: 'https://api.trongrid.io'
});

async function createWallet() {
  const account = await tronWeb.createAccount();
  console.log(JSON.stringify({
    address: account.address.base58,
    privateKey: account.privateKey,
    publicKey: account.publicKey,
  }));
}

createWallet();

