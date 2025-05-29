<?php

class Upload {
    private $arquivo;
    private $altura;
    private $largura;
    private $pasta;

    function __construct($arquivo, $altura, $largura, $pasta) {
        $this->arquivo = $arquivo;
        $this->altura  = $altura;
        $this->largura = $largura;
        $this->pasta   = rtrim($pasta, '/') . '/'; // Garante que a pasta termine com "/"
    }

    private function getExtensao() {
        return strtolower(pathinfo($this->arquivo['name'], PATHINFO_EXTENSION));
    }

    private function ehImagem($extensao) {
        return in_array($extensao, ['gif', 'jpeg', 'jpg', 'png']);
    }

    private function redimensionar($imgLarg, $imgAlt, $tipo, $img_localizacao) {
        // Calcula novo tamanho mantendo a proporção
        if ($imgLarg > $imgAlt) {
            $novaLarg = $this->largura;
            $novaAlt = round(($novaLarg / $imgLarg) * $imgAlt);
        } elseif ($imgAlt > $imgLarg) {
            $novaAlt = $this->altura;
            $novaLarg = round(($novaAlt / $imgAlt) * $imgLarg);
        } else {
            $novaAlt = $novaLarg = max($this->largura, $this->altura);
        }

        // Cria nova imagem
        $novaImagem = imagecreatetruecolor($novaLarg, $novaAlt);

        switch ($tipo) {
            case IMAGETYPE_GIF:
                $origem = imagecreatefromgif($img_localizacao);
                break;
            case IMAGETYPE_JPEG:
                $origem = imagecreatefromjpeg($img_localizacao);
                break;
            case IMAGETYPE_PNG:
                $origem = imagecreatefrompng($img_localizacao);
                // Mantém transparência
                imagealphablending($novaImagem, false);
                imagesavealpha($novaImagem, true);
                break;
            default:
                return;
        }

        imagecopyresampled($novaImagem, $origem, 0, 0, 0, 0, $novaLarg, $novaAlt, $imgLarg, $imgAlt);

        // Salva a imagem de acordo com o tipo
        switch ($tipo) {
            case IMAGETYPE_GIF:
                imagegif($novaImagem, $img_localizacao);
                break;
            case IMAGETYPE_JPEG:
                imagejpeg($novaImagem, $img_localizacao, 90);
                break;
            case IMAGETYPE_PNG:
                imagepng($novaImagem, $img_localizacao);
                break;
        }

        // Libera memória
        imagedestroy($novaImagem);
        imagedestroy($origem);
    }

    public function salvar() {
        $extensao = $this->getExtensao();

        if (!$this->ehImagem($extensao)) {
            return "Formato de arquivo não permitido.";
        }

        // Gera um nome único
        $novo_nome = uniqid() . '.' . $extensao;
        $destino = $this->pasta . $novo_nome;

        // Move o arquivo
        if (!move_uploaded_file($this->arquivo['tmp_name'], $destino)) {
            return "Erro ao mover o arquivo.";
        }

        // Redimensiona se necessário
        list($largura, $altura, $tipo) = getimagesize($destino);
        if ($largura > $this->largura || $altura > $this->altura) {
            $this->redimensionar($largura, $altura, $tipo, $destino);
        }

        return $novo_nome;
    }
}
?>
