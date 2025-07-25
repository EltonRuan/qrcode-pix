<?php
$payload = '';
$qrCodeUrl = '';
$valor = '';
$chave = '';
$nome = '';
$cidade = '';
$descricao = '';
$valorFloat = 0.0;
$formEnviado = false;

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
    return strtoupper(str_pad(dechex($result), 4, '0', STR_PAD_LEFT));
}

function limpar($str) {
    return preg_replace('/[^a-zA-Z0-9@.]/', '', $str);
}

function validarCNPJ($cnpj) {
    $cnpj = preg_replace('/\D/', '', $cnpj);
    return strlen($cnpj) === 14;
}

function validarCPF($cpf) {
    $cpf = preg_replace('/\D/', '', $cpf);
    return strlen($cpf) === 11;
}

function validarTelefone($telefone) {
    $telefone = preg_replace('/\D/', '', $telefone);

    if (substr($telefone, 0, 2) === '55') {
        $telefone = substr($telefone, 2);
    }

    return (strlen($telefone) === 10 || strlen($telefone) === 11);
}


function validarEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'gerar') {
    $formEnviado = true;

    $tipoChave = $_POST['tipo'] ?? '';
    $chaveRaw = trim($_POST['chave'] ?? '');
    $inputValor = str_replace(',', '.', trim($_POST['valor'] ?? '0'));

    $nome = strtoupper(trim($_POST['nome'] ?? ''));
    $cidade = strtoupper(trim($_POST['cidade'] ?? ''));
    $descricao = trim($_POST['descricao'] ?? '');

    $valid = false;

    $chaveExibida = $chaveRaw;

    switch ($tipoChave) {
        case 'cpf':
            if (validarCPF($chaveRaw)) {
                $chave = preg_replace('/\D/', '', $chaveRaw);
                $valid = true;
            }
            break;
        case 'cnpj':
            if (validarCNPJ($chaveRaw)) {
                $chave = preg_replace('/\D/', '', $chaveRaw);
                $valid = true;
            }
            break;
        case 'telefone':
            $chaveLimpa = preg_replace('/\D/', '', $chaveRaw);
            if (substr($chaveLimpa, 0, 2) === '55') {
                $chaveLimpa = substr($chaveLimpa, 2);
            }
            if (validarTelefone($chaveLimpa)) {
                $chave = '+55' . $chaveLimpa; // <-- Isso garante que o payload terá o +55
                $chaveExibida = '+55 ' . substr($chaveLimpa, 0, 2) . ' ' . substr($chaveLimpa, 2, 5) . '-' . substr($chaveLimpa, 7);
                $valid = true;
            }
            break;
        case 'email':
            if (validarEmail($chaveRaw)) {
                $chave = limpar($chaveRaw);
                $valid = true;
            }
            break;
        case 'aleatoria':
            $chave = limpar($chaveRaw);
            $valid = strlen($chave) > 0;
            break;
    }

    $valorFloat = floatval($inputValor);

    if ($valid && $valorFloat > 0 && $nome && $cidade && $descricao) {
        $valor = number_format($valorFloat, 2, '.', '');

    $campo26 = format('00', 'br.gov.bcb.pix') .
            format('01', $chave);

    if (!empty($descricao)) {
        $campo26 .= format('02', $descricao);
    }

    $payload =
        '000201' .
        format('26', $campo26) .
        '52040000' .
        '5303986' .
        format('54', $valor) .
        format('58', 'BR') .
        format('59', $nome) .
        format('60', $cidade) .
        format('62',
            format('05', 'TX' . rand(10000000, 99999999))
        );

    $payload .= '6304';
    $payload .= crc16($payload);


        $payloadUrl = urlencode($payload);
        $qrCodeUrl = "https://quickchart.io/qr?text={$payloadUrl}&size=400";
    } else {
        $payload = '';
        $qrCodeUrl = '';
    }
}
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <title>PIX QR Code com validação e máscara</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 700px;
            margin: 40px auto;
            padding: 20px;
        }
        label, input, select, textarea, button {
            display: block;
            margin-bottom: 10px;
            width: 100%;
        }
        textarea {
            resize: vertical;
        }
        img {
            margin: 10px 0;
            max-width: 300px;
        }
        .error {
            color: red;
            margin-bottom: 10px;
        }
        .logo {
            max-width: 100px;
            margin-bottom: 20px;
        }

          #loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.8);
            z-index: 9999;
            display: none;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: #333;
        }
        .spinner {
            border: 5px solid black;
            border-top: 5px solid #333;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin-bottom: 15px;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }

    </style>
<script>
    function limparFormulario() {
        document.getElementById('tipo').value = '';
        document.getElementById('chave').value = '';
        document.getElementById('valor').value = '';
        document.getElementById('nome').value = '';
        document.getElementById('cidade').value = '';
        document.getElementById('descricao').value = '';

        const resultado = document.getElementById('resultado');
        if (resultado) {
            resultado.remove();
        }
        const erro = document.getElementById('erro');
        if (erro) {
            erro.remove();
        }
    }

    function formatarChave(tipo, valor) {
        valor = valor.replace(/\s+/g, '').trim();

        if (tipo === 'cpf') {
            valor = valor.replace(/\D/g, '');
            valor = valor.replace(/^(\d{3})(\d)/, '$1.$2');
            valor = valor.replace(/^(\d{3})\.(\d{3})(\d)/, '$1.$2.$3');
            valor = valor.replace(/\.(\d{3})(\d)/, '.$1-$2');
            if (valor.length > 14) valor = valor.substr(0, 14);

        } else if (tipo === 'cnpj') {
            valor = valor.replace(/\D/g, '');
            valor = valor.replace(/^(\d{2})(\d)/, '$1.$2');
            valor = valor.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
            valor = valor.replace(/\.(\d{3})(\d)/, '.$1/$2');
            valor = valor.replace(/(\d{4})(\d)/, '$1-$2');
            if (valor.length > 18) valor = valor.substr(0, 18);

        } else if (tipo === 'telefone') {
            valor = valor.replace(/\D/g, '');
            if (valor.startsWith('55') === false) {
                valor = '55' + valor;
            }
            valor = valor.replace(/^55(\d{2})(\d{5})(\d{4}).*/, '+55 $1 $2-$3');
            if (valor.length > 17) valor = valor.substr(0, 17);

        } else if (tipo === 'email') {
            valor = valor.replace(/\s/g, '');
            if (valor.length > 60) valor = valor.substr(0, 60);
        }

        return valor;
    }

    function aplicarMascara() {
        const tipo = document.getElementById('tipo').value;
        let valor = document.getElementById('chave').value;

        if (tipo === '') return;

        valor = formatarChave(tipo, valor);
        document.getElementById('chave').value = valor;
    }

    window.onload = function () {
        document.getElementById('tipo').addEventListener('change', function () {
            document.getElementById('chave').value = '';
        });
        document.getElementById('chave').addEventListener('input', aplicarMascara);
    }
</script>

</head>
<body>

    <div id="loading-overlay">
    <div style="text-align: center;">
        <div class="spinner"></div>
    </div>
    </div>

    <h1>Gerar QR Code PIX</h1>

    <?php if ($formEnviado && !$payload): ?>
        <div id="erro" class="error">Erro: Preencha corretamente todos os campos e a chave PIX no formato correto conforme o tipo.</div>
    <?php endif; ?>

    <?php if (!$qrCodeUrl): ?>
    <form method="post" action="">
        <input type="hidden" name="acao" value="gerar" />

        <label for="tipo">Tipo da chave:</label>
        <select name="tipo" id="tipo" required>
            <option value="">Selecione</option>
            <option value="cpf" <?= ($_POST['tipo'] ?? '') === 'cpf' ? 'selected' : '' ?>>CPF</option>
            <option value="cnpj" <?= ($_POST['tipo'] ?? '') === 'cnpj' ? 'selected' : '' ?>>CNPJ</option>
            <option value="telefone" <?= ($_POST['tipo'] ?? '') === 'telefone' ? 'selected' : '' ?>>Telefone</option>
            <option value="email" <?= ($_POST['tipo'] ?? '') === 'email' ? 'selected' : '' ?>>E-mail</option>
        </select>

        <label for="chave">Chave PIX:</label>
        <input type="text" id="chave" name="chave" value="<?= $formEnviado ? htmlspecialchars($_POST['chave'] ?? '') : '' ?>" required />

        <label for="valor">Valor (ex: 10,00):</label>
        <input type="text" id="valor" name="valor" value="<?= $formEnviado ? htmlspecialchars($_POST['valor'] ?? '') : '' ?>" required />

        <label for="nome">Nome do recebedor:</label>
        <input type="text" id="nome" name="nome" value="<?= $formEnviado ? htmlspecialchars($_POST['nome'] ?? '') : '' ?>" required />

        <label for="cidade">Cidade do recebedor:</label>
        <input type="text" id="cidade" name="cidade" value="<?= $formEnviado ? htmlspecialchars($_POST['cidade'] ?? '') : '' ?>" required />

        <label for="descricao">Descrição (ex: Pagamento):</label>
        <input type="text" id="descricao" name="descricao" value="<?= $formEnviado ? htmlspecialchars($_POST['descricao'] ?? '') : '' ?>" required />

        <button type="submit">Gerar QR Code</button>
        <button type="button" onclick="limparFormulario()">Limpar</button>
    </form>

    <?php endif; ?>

    <?php if ($formEnviado && $payload && $qrCodeUrl): ?>
        <div id="resultado">
            <hr>
            <p><strong>Chave usada:</strong> <?= htmlspecialchars($chaveExibida) ?></p>
            <p><strong>Valor:</strong> R$ <?= number_format($valorFloat, 2, ',', '.') ?></p>
            <img src="<?= $qrCodeUrl ?>" alt="QR Code PIX" />
            <p><strong>Payload PIX gerado:</strong></p>
            <textarea rows="6" readonly><?= $payload ?></textarea>
        </div>

        <form method="GET">
            <button type="submit" style="margin-top: 20px; padding: 10px 20px;">Gerar novo</button>
        </form>
    <?php endif; ?>

    <script>
        const form = document.querySelector("form");
        const overlay = document.getElementById("loading-overlay");

        form?.addEventListener("submit", function () {
            overlay.style.display = "flex";
        });
    </script>

</body>
</html>
