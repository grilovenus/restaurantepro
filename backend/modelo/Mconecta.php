<?php

class Mconecta {
    private static $conn = null;

    public static function conecta() {
        if (self::$conn instanceof \PDO) {
            return self::$conn; // Reutiliza a conexão ativa
        }

        $USER = 'root';
        $DB = 'irg';
        $HOST = '127.0.0.1';
        $PORT = '3306';
        $PASS = '';

        try {
            self::$conn = new \PDO("mysql:host=$HOST;port=$PORT;dbname=$DB;charset=utf8", $USER, $PASS, [
                \PDO::ATTR_PERSISTENT => true,
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
            ]);
        } catch (\PDOException $e) {
            error_log("Erro de conexão: " . $e->getMessage());
            return null;
        }

        return self::$conn;
    }

    public static function desconectar() {
        self::$conn = null; // Fecha a conexão se necessário
    }
}
