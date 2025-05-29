<?php

class Utils {

    private static $mysql;

    /**
     * Calcula a distância entre dois pontos geográficos (latitude e longitude) usando a fórmula de Haversine.
     *
     * @param float $lat1 Latitude do primeiro ponto.
     * @param float $lng1 Longitude do primeiro ponto.
     * @param float $lat2 Latitude do segundo ponto.
     * @param float $lng2 Longitude do segundo ponto.
     * @return float Distância entre os dois pontos em quilômetros.
     */
    public static function calcularDistancia($lat1, $lng1, $lat2, $lng2) {
        // Convertendo de graus para radianos
        $lat1 = deg2rad($lat1);
        $lng1 = deg2rad($lng1);
        $lat2 = deg2rad($lat2);
        $lng2 = deg2rad($lng2);

        // Fórmula de Haversine
        $dlat = $lat2 - $lat1;
        $dlng = $lng2 - $lng1;

        $a = sin($dlat / 2) * sin($dlat / 2) + cos($lat1) * cos($lat2) * sin($dlng / 2) * sin($dlng / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        // Raio da Terra em quilômetros (6371 km)
        $distancia = 6371 * $c;

        return $distancia;
    }

    public static function calcularAjustes(float $total, float $jurosPercentual, float $multa, float $desconto, float $correcao, ?string $dataLancamento): array {
        $jurosCalculado = 0;

        // Calcula a quantidade de meses entre a data de lançamento e hoje, se fornecida
        if ($dataLancamento) {
            $dataAtual = new DateTime(); // Data atual
            $dataLancamentoObj = DateTime::createFromFormat('Y-m-d', $dataLancamento); // Data de lançamento

            if ($dataLancamentoObj && $dataLancamentoObj < $dataAtual) {
                $intervalo = $dataAtual->diff($dataLancamentoObj);
                $mesesAtraso = $intervalo->m + ($intervalo->y * 12); // Total de meses de atraso
                $jurosCalculado = $mesesAtraso * ($jurosPercentual / 100); // Juros acumulados (percentual)
            }
        }

        // Calcula ajustes
        $valorJuros = $total * $jurosCalculado;
        $valorMulta = $total * ($multa / 100);
        $valorDesconto = $total * ($desconto / 100);
        $valorCorrecao = $total * ($correcao / 100);

        // Aplica ajustes ao total
        $totalComAjustes = $total + $valorJuros + $valorMulta + $valorCorrecao - $valorDesconto;

        return [
            'total' => round($totalComAjustes, 2),
            'juros' => round($valorJuros, 2),
            'multa' => round($valorMulta, 2),
            'desconto' => round($valorDesconto, 2),
            'correcao' => round($valorCorrecao, 2),
            'principal' => round($total, 2),
        ];
    }

    /**
     * Processa a imagem enviada, valida e salva no diretório especificado.
     * 
     * @param array $image Dados do arquivo enviado ($_FILES['campo']).
     * @param string $uploadDir Caminho do diretório para salvar a imagem.
     * @param array $allowedExtensions Extensões permitidas (padrão: jpg, jpeg, png, gif).
     * 
     * @return string|null Nome do arquivo salvo ou null em caso de erro.
     */
    public static function processImage(array $image, string $uploadDir = '../uploads/motoristas_perfil/', string $customFileName = '', array $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif']): ?string {
        // Cria o diretório de upload, se não existir
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Verifica erros no upload
        if ($image['error'] !== UPLOAD_ERR_OK) {
            return null; // Ou lance uma exceção caso prefira tratar o erro no chamador
        }

        // Valida a extensão do arquivo
        $fileExtension = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION));
        if (!in_array($fileExtension, $allowedExtensions)) {
            return null; // Ou lance uma exceção para indicar formato inválido
        }

        // Se um nome personalizado for fornecido, use ele. Caso contrário, gere um nome único
        if ($customFileName) {
            $newFileName = $customFileName . '.' . $fileExtension;
        } else {
            $newFileName = uniqid('id_', true) . '.' . $fileExtension;
        }

        $filePath = $uploadDir . $newFileName;

        // Move o arquivo para o diretório de upload
        if (!move_uploaded_file($image['tmp_name'], $filePath)) {
            return null; // Ou lance uma exceção para indicar erro no upload
        }

        return $newFileName;
    }

    /**
     * Remove valores nulos de um array.
     * 
     * @param array $data Array para filtrar.
     * 
     * @return array Array sem valores nulos.
     */
    public static function filterNullValues(array $data): array {
        return array_filter($data, fn($value) => $value !== null);
    }

    /**
     * Gera uma resposta JSON padronizada.
     * 
     * @param bool $success Indica sucesso ou falha.
     * @param string $message Mensagem de retorno.
     * @param mixed $data Dados adicionais (opcional).
     * 
     * @return void
     */
    public static function jsonResponse(bool $success, string $message, $data = null): void {
        echo json_encode([
            "resultado" => $success,
            "texto" => $message,
            "dados" => $data
        ]);
        exit;
    }

    // Função para excluir a imagem do diretório
    public static function deleteImage($imagePath): void {
        if (file_exists($imagePath)) {
            // Exclui o arquivo de imagem
            unlink($imagePath);
        } else {
            // Caso a imagem não seja encontrada
            echo "Arquivo de imagem não encontrado: " . $imagePath;
        }
    }

    // Valida CPF
    public static function isValidCPF(string $cpf): bool {
        // Remove caracteres não numéricos do CPF
        $cpf = preg_replace('/[^0-9]/', '', $cpf);

        // Verifica se o CPF tem 11 dígitos e se todos os dígitos são iguais
        if (strlen($cpf) != 11 || preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        // Loop para calcular os dígitos verificadores
        for ($t = 9; $t < 11; $t++) {
            $soma = 0;
            for ($c = 0; $c < $t; $c++) {
                // Calcula a soma ponderada dos dígitos
                $soma += (int) $cpf[$c] * (($t + 1) - $c);
            }

            // Calcula o dígito verificador
            $digito_verificador = ((10 * $soma) % 11) % 10;

            // Verifica se o dígito calculado é igual ao dígito informado
            if ((int) $cpf[$t] != $digito_verificador) {
                return false;
            }
        }

        // CPF válido
        return true;
    }

    // Valida CNPJ
    public static function isValidCNPJ(string $cnpj): bool {
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);

        if (strlen($cnpj) != 14) {
            return false;
        }

        $calcDigits = function ($cnpj, $positions = 5) {
            $sum = 0;
            for ($i = 0;
                    $i < strlen($cnpj);
                    $i++) {
                $sum += $cnpj[$i] * $positions;
                $positions = $positions == 2 ? 9 : $positions - 1;
            }
            $digit = $sum % 11 < 2 ? 0 : 11 - ($sum % 11);
            return $digit;
        };

        $baseCNPJ = substr($cnpj, 0, 12);
        $digit1 = $calcDigits($baseCNPJ);
        $digit2 = $calcDigits($baseCNPJ . $digit1, 6);

        return $cnpj == $baseCNPJ . $digit1 . $digit2;
    }

    // Valida Email
    public static function isValidEmail(string $email): bool {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    // Valida CEP
    public static function isValidCEP(string $cep): bool {
        return preg_match('/^[0-9]{5}-?[0-9]{3}$/', $cep);
    }

    // Valida decimal com ponto ou vírgula
    public static function isValidDecimal(string $value): bool {
        return preg_match('/^-?\d+(\.\d+|,\d+)?$/', $value);
    }

    public static function decimalUsToBr(string $value, bool $formatar = false): string {
        // Remove os separadores de milhar (vírgulas)
        $noThousands = str_replace(',', '', $value);
        // Substitui o ponto pelo separador decimal brasileiro (vírgula)
        $convertedValue = str_replace('.', ',', $noThousands);

        // Se formatar for verdadeiro, formata o número no padrão brasileiro
        if ($formatar) {
            $convertedValue = number_format(floatval($convertedValue), 2, ',', '.');
        }

        return $convertedValue;
    }

    public static function decimalBrToUs(string $value, bool $formatar = false): string {
        // Remove os separadores de milhar (pontos)
        $noThousands = str_replace('.', '', $value);
        // Substitui a vírgula pelo separador decimal americano (ponto)
        $convertedValue = str_replace(',', '.', $noThousands);

        // Se formatar for verdadeiro, formata o número no padrão americano
        if ($formatar) {
            $convertedValue = number_format(floatval($convertedValue), 2, '.', ',');
        }

        return $convertedValue;
    }

    /**
     * Remove todos os caracteres, mantendo apenas números e letras.
     * 
     * @param string $input A string que será limpa.
     * 
     * @return string A string contendo apenas números e letras.
     */
    public static function removeAlfanumericos(string $input): string {
        // Usa expressão regular para remover tudo, exceto letras e números
        return preg_replace('/[^a-zA-Z0-9]/', '', $input);
    }

    /**
     * Remove apenas os números da string.
     * 
     * @param string $input A string que será limpa.
     * 
     * @return string A string contendo apenas as letras e outros caracteres.
     */
    public static function removeNumeros(string $input): string {
        // Remove todos os números
        return preg_replace('/[0-9]/', '', $input);
    }

    /**
     * Remove apenas as letras da string.
     * 
     * @param string $input A string que será limpa.
     * 
     * @return string A string contendo apenas os números e outros caracteres.
     */
    public static function removeLetras(string $input): string {
        // Remove todas as letras (maiúsculas e minúsculas)
        return preg_replace('/[a-zA-Z]/', '', $input);
    }

    /**
     * Mantém apenas os números da string.
     * 
     * @param string $input A string que será limpa.
     * 
     * @return string A string contendo apenas os números.
     */
    public static function manterNumeros(string $input): string {
        // Remove tudo, exceto os números
        return preg_replace('/\D/', '', $input);
    }

    /**
     * Mantém apenas as letras da string.
     * 
     * @param string $input A string que será limpa.
     * 
     * @return string A string contendo apenas as letras.
     */
    public static function manterLetras(string $input): string {
        // Remove tudo, exceto as letras
        return preg_replace('/[^a-zA-Z]/', '', $input);
    }

    /**
     * Remove todos os números e letras da string, mantendo apenas os caracteres especiais.
     * 
     * @param string $input A string que será limpa.
     * 
     * @return string A string contendo apenas os caracteres especiais.
     */
    public static function removeNumerosLetras(string $input): string {
        // Remove todas as letras e números
        return preg_replace('/[a-zA-Z0-9]/', '', $input);
    }

    public static function enviarEmail($para, $assunto, $mensagem) {
        $headers = "From: noreply@qcarona.com.br\r\n" .
                "Reply-To: noreply@qcarona.com.br\n" .
                "X-Mailer: PHP/" . phpversion();

        try {
            if (mail($para, $assunto, $mensagem, $headers)) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            error_log($e->getMessage(), 0);
            return false;
        }
    }

    public static function criptografar($texto, $senha) {
        $chave = hash('sha256', $senha, true);
        $ivlen = openssl_cipher_iv_length('AES-256-CBC');
        $iv = openssl_random_pseudo_bytes($ivlen);

        if ($iv === false) {
            throw new Exception('Erro ao gerar IV seguro.');
        }

        $texto_criptografado = openssl_encrypt($texto, 'AES-256-CBC', $chave, OPENSSL_RAW_DATA, $iv);
        if ($texto_criptografado === false) {
            throw new Exception('Erro na criptografia.');
        }

        // Codifica o IV e o texto criptografado em hexadecimal
        return bin2hex($iv . $texto_criptografado);
    }

    public static function descriptografar($texto_criptografado_hex, $senha) {
        // Decodifica o texto criptografado de hexadecimal
        $texto_criptografado = hex2bin($texto_criptografado_hex);

        $chave = hash('sha256', $senha, true);
        $ivlen = openssl_cipher_iv_length('AES-256-CBC');
        $iv = substr($texto_criptografado, 0, $ivlen);
        $texto_criptografado = substr($texto_criptografado, $ivlen);

        $texto_descriptografado = openssl_decrypt($texto_criptografado, 'AES-256-CBC', $chave, OPENSSL_RAW_DATA, $iv);
        if ($texto_descriptografado === false) {
            throw new Exception('Erro na descriptografia.');
        }

        return $texto_descriptografado;
    }
}
