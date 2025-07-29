<?php
require_once 'QrcodePixClass.php';

$qrcode = new QrcodePixClass();

$payload = '';
$qrCodeUrl = '';
$valorFloat = 0.0;
$chaveExibida = '';
$formEnviado = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['acao'] === 'gerar') {
    $resultado = $qrcode->gerarPayload(
        $_POST['tipo'] ?? '',
        trim($_POST['chave'] ?? ''),
        $_POST['valor'] ?? '',
        $_POST['nome'] ?? '',
        $_POST['cidade'] ?? '',
        $_POST['descricao'] ?? ''
    );

    if ($resultado) {
        $payload = $resultado['payload'];
        $qrCodeUrl = $resultado['qrCodeUrl'];
        $chaveExibida = $resultado['chaveExibida'];
        $valorFloat = $resultado['valorFloat'];
        $formEnviado = true;
    } else {
        $formEnviado = true;
    }
}
?>


<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <title>PIX QR Code com validação e máscara</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style src="assets/style.css"></style>

    <script src="assets/script.js"></script>

</head>

<body class="min-h-screen w-full bg-gray-100 flex flex-col items-center justify-center p-6">

    <div id="loading-overlay" class="fixed inset-0 bg-white bg-opacity-75 flex items-center justify-center hidden z-50">
        <div class="w-16 h-16 border-4 border-blue-500 border-dashed rounded-full animate-spin"></div>
    </div>

    <h1 class="text-3xl font-bold text-blue-600 mb-6">Gerar QR Code PIX</h1>

    <?php if ($formEnviado && !$payload): ?>
        <div id="erro" class="bg-red-100 text-red-800 px-4 py-2 rounded mb-4 max-w-md w-full text-center">
            Erro: Preencha corretamente todos os campos e a chave PIX no formato correto conforme o tipo.
        </div>
    <?php endif; ?>

    <?php if (!$qrCodeUrl): ?>
        <form method="post" action="" class="bg-white shadow-md rounded p-6 space-y-4 w-full max-w-md">
            <input type="hidden" name="acao" value="gerar" />

            <div>
                <label for="tipo" class="block font-medium text-gray-700">Tipo da chave:</label>
                <select name="tipo" id="tipo" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Selecione</option>
                    <option value="cpf" <?= ($_POST['tipo'] ?? '') === 'cpf' ? 'selected' : '' ?>>CPF</option>
                    <option value="cnpj" <?= ($_POST['tipo'] ?? '') === 'cnpj' ? 'selected' : '' ?>>CNPJ</option>
                    <option value="telefone" <?= ($_POST['tipo'] ?? '') === 'telefone' ? 'selected' : '' ?>>Telefone</option>
                    <option value="email" <?= ($_POST['tipo'] ?? '') === 'email' ? 'selected' : '' ?>>E-mail</option>
                </select>
            </div>

            <div>
                <label for="chave" class="block font-medium text-gray-700">Chave PIX:</label>
                <input type="text" id="chave" name="chave"
                    value="<?= $formEnviado ? htmlspecialchars($_POST['chave'] ?? '') : '' ?>" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500" />
            </div>

            <div>
                <label for="valor" class="block font-medium text-gray-700">Valor (ex: 10,00):</label>
                <input type="text" id="valor" name="valor"
                    value="<?= $formEnviado ? htmlspecialchars($_POST['valor'] ?? '') : '' ?>" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500" />
            </div>

            <div>
                <label for="nome" class="block font-medium text-gray-700">Nome do recebedor:</label>
                <input type="text" id="nome" name="nome"
                    value="<?= $formEnviado ? htmlspecialchars($_POST['nome'] ?? '') : '' ?>" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500" />
            </div>

            <div>
                <label for="cidade" class="block font-medium text-gray-700">Cidade do recebedor:</label>
                <input type="text" id="cidade" name="cidade"
                    value="<?= $formEnviado ? htmlspecialchars($_POST['cidade'] ?? '') : '' ?>" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500" />
            </div>

            <div>
                <label for="descricao" class="block font-medium text-gray-700">Descrição (ex: Pagamento):</label>
                <input type="text" id="descricao" name="descricao"
                    value="<?= $formEnviado ? htmlspecialchars($_POST['descricao'] ?? '') : '' ?>" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500" />
            </div>

            <div class="flex justify-between gap-2">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded w-1/2">Gerar QR Code</button>
                <button type="button" onclick="limparFormulario()"
                    class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded w-1/2">Limpar</button>
            </div>
        </form>
    <?php endif; ?>

    <?php if ($formEnviado && $payload && $qrCodeUrl): ?>
        <div id="resultado" class="bg-white shadow-md rounded p-6 mt-6 max-w-md w-full text-center space-y-4">
            <p><strong>Chave usada:</strong> <?= htmlspecialchars($chaveExibida) ?></p>
            <p><strong>Valor:</strong> R$ <?= number_format($valorFloat, 2, ',', '.') ?></p>
            <img src="<?= $qrCodeUrl ?>" alt="QR Code PIX" class="mx-auto max-w-xs" />
            <div>
                <p class="font-medium text-gray-700">Payload PIX gerado:</p>
                <textarea rows="6" readonly
                    class="w-full mt-2 p-2 border rounded text-sm bg-gray-50 text-gray-700"><?= $payload ?></textarea>
            </div>
        </div>

        <form method="GET" class="mt-4">
            <button type="submit"
                class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-6 rounded">Gerar novo</button>
        </form>
    <?php endif; ?>

</body>

</html>