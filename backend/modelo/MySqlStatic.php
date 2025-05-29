<?php

include_once "Mconecta.php";

class MySqlStatic {

    private static $pdo;

    // Conectar ao banco de dados - método estático
    public static function conecta(): bool {
        if (!self::$pdo) {
            try {
                self::$pdo = Mconecta::conecta();
                return true;
            } catch (PDOException $e) {
                die("Erro ao conectar ao banco de dados: " . $e->getMessage());
            }
        }
        return true;  // Conexão já estabelecida
    }

    // Inserir dados e retornar o objeto inserido com o ID gerado
    public static function insert(string $table, array $data): ?object {
        self::conecta();  // Garante que a conexão esteja aberta
        $columns = implode(", ", array_keys($data));
        $placeholders = ":" . implode(", :", array_keys($data));
        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";

        try {
            $stmt = self::$pdo->prepare($sql);
            if ($stmt->execute($data)) {
                $lastInsertId = self::$pdo->lastInsertId();
                return self::obj($table, $lastInsertId);  // Retorna o objeto inserido
            }
            return null;  // Retorna null se a inserção falhar
        } catch (PDOException $e) {
            self::logError($e);
            return null;  // Retorna null em caso de erro
        }
    }

// Atualizar dados de uma tabela
    public static function update(string $table, array $data, $id = null, string $condition = '1=1', array $params = [], string $key = 'id'): ?object {
        self::conecta();  // Garante que a conexão esteja aberta
        // Adiciona o ID na condição, se fornecido
        if ($id !== null) {
            $params[$key] = $id;
            $condition = "$key = :$key";
        }

        // Monta a string de atualização (SET)
        $set = implode(", ", array_map(fn($field) => "$field = :$field", array_keys($data)));
        $sql = "UPDATE $table SET $set WHERE $condition";

        try {
            // Prepara e executa a consulta
            $stmt = self::$pdo->prepare($sql);
            $executed = $stmt->execute(array_merge($data, $params));

            // Retorna o objeto atualizado se a execução for bem-sucedida
            if ($executed && isset($params[$key])) {
                return self::obj($table, $params[$key]);
            }

            return null;  // Retorna null se a execução falhar ou não houver chave
        } catch (PDOException $e) {
            self::logError($e);  // Log do erro
            return null;  // Retorna null em caso de erro
        }
    }

    public static function delete(string $table, $id = null, string $condition = '1=1', array $params = [], string $key = 'id'): bool {
        self::conecta();  // Garante que a conexão esteja aberta
        // Adiciona o ID na condição, se fornecido
        if ($id !== null) {
            $params[$key] = $id;
            $condition = "$key = :$key";
        }

        $sql = "DELETE FROM $table WHERE $condition";

        try {
            // Prepara e executa a consulta
            $stmt = self::$pdo->prepare($sql);
            return $stmt->execute($params);  // Retorna true ou false com base no sucesso
        } catch (PDOException $e) {
            self::logError($e);  // Log do erro
            return false;  // Retorna false em caso de erro
        }
    }

    // Selecionar dados de uma tabela
    public static function select(string $table, string $columns = "*", string $condition = "", array $params = [], string $orderBy = "", string $limit = ""): array {
        self::conecta();  // Garante que a conexão esteja aberta
        $sql = "SELECT $columns FROM $table";
        if (!empty($condition)) {
            $sql .= " WHERE $condition";
        }
        if (!empty($orderBy)) {
            $sql .= " ORDER BY $orderBy";
        }
        if (!empty($limit)) {
            $sql .= " LIMIT $limit";
        }
        try {
            $stmt = self::$pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            self::logError($e);
            return [];  // Retorna um array vazio em caso de erro
        }
    }

    // Buscar um único registro da tabela
    public static function obj(string $table, $id = null, string $columns = "*", string $conditions = "1=1", array $params = []): ?object {
        self::conecta();  // Garante que a conexão esteja aberta
        // Se o ID for fornecido, adiciona ao filtro de condições
        if ($id !== null) {
            $params['id'] = $id;
            $conditions = 'id = :id';
        }

        // Construção da consulta com a condição e os parâmetros
        $sql = "SELECT $columns FROM $table WHERE $conditions LIMIT 1";

        try {
            // Prepara e executa a consulta
            $stmt = self::$pdo->prepare($sql);
            $stmt->execute($params);

            // Retorna o objeto ou null se não houver resultados
            $result = $stmt->fetch(PDO::FETCH_OBJ);
            return $result ?: null;  // Retorna o objeto ou null se não encontrar nada
        } catch (PDOException $e) {
            self::logError($e);  // Log do erro
            return null;  // Retorna null em caso de erro
        }
    }

    // Função de log de erro
    private static function logError(PDOException $e): void {
        error_log("[" . date("Y-m-d H:i:s") . "] " . $e->getMessage() . PHP_EOL, 3, "errors.log");
    }
}
