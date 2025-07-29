<?php

class QrcodePixClass
{
    public $payload = '';
    public $qrCodeUrl = '';

    public function gerarPayload($tipoChave, $chaveRaw, $valor, $nome, $cidade, $descricao)
    {
        $chave = '';
        $valid = false;
        $valorFloat = floatval(str_replace(',', '.', $valor));
        $chaveExibida = $chaveRaw;

        switch ($tipoChave) {
            case 'cpf':
                if ($this->validarCPF($chaveRaw)) {
                    $chave = preg_replace('/\D/', '', $chaveRaw);
                    $valid = true;
                }
                break;

            case 'cnpj':
                if ($this->validarCNPJ($chaveRaw)) {
                    $chave = preg_replace('/\D/', '', $chaveRaw);
                    $valid = true;
                }
                break;

            case 'telefone':
                $chaveLimpa = preg_replace('/\D/', '', $chaveRaw);
                if (substr($chaveLimpa, 0, 2) === '55') {
                    $chaveLimpa = substr($chaveLimpa, 2);
                }
                if ($this->validarTelefone($chaveLimpa)) {
                    $chave = '+55' . $chaveLimpa;
                    $chaveExibida = '+55 ' . substr($chaveLimpa, 0, 2) . ' ' . substr($chaveLimpa, 2, 5) . '-' . substr($chaveLimpa, 7);
                    $valid = true;
                }
                break;

            case 'email':
                if ($this->validarEmail($chaveRaw)) {
                    $chave = $this->limpar($chaveRaw);
                    $valid = true;
                }
                break;

            case 'aleatoria':
                $chave = $this->limpar($chaveRaw);
                $valid = strlen($chave) > 0;
                break;
        }

        if ($valid && $valorFloat > 0 && $nome && $cidade && $descricao) {
            $valor = number_format($valorFloat, 2, '.', '');

            $campo26 = $this->format('00', 'br.gov.bcb.pix') .
                       $this->format('01', $chave);

            if (!empty($descricao)) {
                $campo26 .= $this->format('02', $descricao);
            }

            $payload =
                '000201' .
                $this->format('26', $campo26) .
                '52040000' .
                '5303986' .
                $this->format('54', $valor) .
                $this->format('58', 'BR') .
                $this->format('59', strtoupper($nome)) .
                $this->format('60', strtoupper($cidade)) .
                $this->format('62', $this->format('05', 'TX' . rand(10000000, 99999999)));

            $payload .= '6304';
            $payload .= $this->crc16($payload);

            $this->payload = $payload;
            $this->qrCodeUrl = "https://quickchart.io/qr?text=" . urlencode($payload);
            return [
                'payload' => $payload,
                'qrCodeUrl' => $this->qrCodeUrl,
                'chaveExibida' => $chaveExibida,
                'valorFloat' => $valorFloat
            ];
        }

        return null;
    }

    private function format($id, $value)
    {
        $length = strlen($value);
        return $id . str_pad($length, 2, '0', STR_PAD_LEFT) . $value;
    }

    private function crc16($str)
    {
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

    private function limpar($str)
    {
        return preg_replace('/[^a-zA-Z0-9@.]/', '', $str);
    }

    private function validarCNPJ($cnpj)
    {
        $cnpj = preg_replace('/\D/', '', $cnpj);
        return strlen($cnpj) === 14;
    }

    private function validarCPF($cpf)
    {
        $cpf = preg_replace('/\D/', '', $cpf);
        return strlen($cpf) === 11;
    }

    private function validarTelefone($telefone)
    {
        $telefone = preg_replace('/\D/', '', $telefone);
        if (substr($telefone, 0, 2) === '55') {
            $telefone = substr($telefone, 2);
        }
        return (strlen($telefone) === 10 || strlen($telefone) === 11);
    }

    private function validarEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}
