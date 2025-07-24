<?php
// Variáveis padrão
$chave = '47337756879';
$nome = 'ELTON RUAN';
$cidade = 'SAO PAULO';
$descricao = 'Pagamento teste';
$txid = 'TX12345678';
$valor = '0.00';
$payload = '';
$qrCodeUrl = '';

function format($id, $value) {
    $length = strlen($value);
    return $id . str_pad($length, 2, '0', STR_PAD_LEFT) . $value;
}

function crc16($str) {
    $polynomial = 0x1021;
    $result = 0xFFFF;

    for ($i = 0; $i < strlen($str); $i++) {
        $result ^= (ord($str[$i]) << 8);
        for ($bit = 0; $bit < 8; $bit++) {
            if (($result << 1) & 0x10000) {
                $result = (($result << 1) ^ $polynomial) & 0xFFFF;
            } else {
                $result = ($result << 1) & 0xFFFF;
            }
        }
    }
    return strtoupper(str_pad(dechex($result),4,'0',STR_PAD_LEFT));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recebe dados do form
    $inputValor = str_replace(',', '.', $_POST['valor'] ?? '0');
    $valorFloat = floatval($inputValor);
    $inputChave = trim($_POST['chave'] ?? '');
    $inputCidade = trim($_POST['cidade'] ?? '');
    $inputDescricao = trim($_POST['descricao'] ?? '');

    if ($valorFloat > 0 && $inputChave !== '') {
        $valor = number_format($valorFloat, 2, '.', '');
        $chave = $inputChave;
        $cidade = $inputCidade !== '' ? $inputCidade : $cidade;
        $descricao = $inputDescricao !== '' ? $inputDescricao : $descricao;

        // Gera o payload com os dados do formulário
        $payload =
            '000201' .
            format('26',
                format('00', 'br.gov.bcb.pix') .
                format('01', $chave) .
                format('02', $descricao)
            ) .
            '52040000' .
            '5303986' .
            format('54', $valor) .
            format('58', 'BR') .
            format('59', $nome) .
            format('60', $cidade) .
            format('62',
                format('05', $txid)
            );

        $payload .= '6304';
        $payload .= crc16($payload);

        $payloadUrl = urlencode($payload);
        $qrCodeUrl = "https://quickchart.io/qr?text={$payloadUrl}&size=400";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <title>PIX QR Code</title>
</head>
<body>
    <h1>Gerar QR Code PIX</h1>
    <form method="post" action="">
        <label for="chave">Chave PIX (CPF, CNPJ, e-mail, telefone ou aleatória): </label>
        <input type="text" id="chave" name="chave" value="<?= isset($_POST['chave']) ? htmlspecialchars($_POST['chave']) : $chave ?>" required />
        <br><br>

        <label for="cidade">Cidade: </label>
        <input type="text" id="cidade" name="cidade" value="<?= isset($_POST['cidade']) ? htmlspecialchars($_POST['cidade']) : $cidade ?>" required />
        <br><br>

        <label for="descricao">Descrição: </label>
        <input type="text" id="descricao" name="descricao" value="<?= isset($_POST['descricao']) ? htmlspecialchars($_POST['descricao']) : $descricao ?>" />
        <br><br>

        <label for="valor">Valor (ex: 10,00): </label>
        <input type="text" id="valor" name="valor" value="<?= isset($_POST['valor']) ? htmlspecialchars($_POST['valor']) : '' ?>" required />
        <br><br>

        <button type="submit">Gerar</button>
    </form>

    <?php if ($payload): ?>
        <p><strong>Valor:</strong> R$ <?= number_format($valorFloat, 2, ',', '.') ?></p>
        <p><strong>Chave PIX:</strong> <?= htmlspecialchars($chave) ?></p>
        <p><strong>Cidade:</strong> <?= htmlspecialchars($cidade) ?></p>
        <p><strong>Descrição:</strong> <?= htmlspecialchars($descricao) ?></p>
        <img src="<?= $qrCodeUrl ?>" alt="QR Code PIX" />
        <p><strong>Payload PIX gerado:</strong></p>
        <textarea rows="5" cols="80" readonly><?= $payload ?></textarea>
    <?php endif; ?>
</body>
</html>
