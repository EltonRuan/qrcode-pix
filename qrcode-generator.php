<?php
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
    $inputValor = str_replace(',', '.', $_POST['valor'] ?? '0');
    $valorFloat = floatval($inputValor);
    if ($valorFloat > 0) {
        $valor = number_format($valorFloat, 2, '.', '');
        
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
    <title>PIX QR Code sem biblioteca</title>
</head>
<body>
    <h1>Gerar QR Code PIX</h1>
    <form method="post" action="">
        <label for="valor">Valor (ex: 10,00): </label>
        <input type="text" id="valor" name="valor" value="<?= isset($_POST['valor']) ? htmlspecialchars($_POST['valor']) : '' ?>" required />
        <button type="submit">Gerar</button>
    </form>

    <?php if ($payload): ?>
        <p><strong>Valor:</strong> R$ <?= number_format($valorFloat, 2, ',', '.') ?></p>
        <img src="<?= $qrCodeUrl ?>" alt="QR Code PIX" />
        <p><strong>Payload PIX gerado:</strong></p>
        <textarea rows="5" cols="80" readonly><?= $payload ?></textarea>
    <?php endif; ?>
</body>
</html>
