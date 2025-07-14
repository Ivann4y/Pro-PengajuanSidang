<?php

abstract class Model {
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function find($id) {
        $sql = "SELECT * FROM [dbo].[{$this->table}] WHERE [{$this->primaryKey}] = ?";
        return $this->db->fetchOne($sql, [$id]);
    }
    
    public function findAll($conditions = [], $orderBy = null, $limit = null) {
        $sql = "SELECT * FROM [dbo].[{$this->table}]";
        $params = [];
        
        if (!empty($conditions)) {
            $whereClause = [];
            foreach ($conditions as $column => $value) {
                $whereClause[] = "[{$column}] = ?";
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $whereClause);
        }
        
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }
        
        if ($limit) {
            $sql .= " LIMIT {$limit}";
        }
        
        return $this->db->fetchAll($sql, $params);
    }
    
    public function create($data) {
        $data = $this->filterFillable($data);
        
        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');
        
        $sql = "INSERT INTO [dbo].[{$this->table}] ([{$this->primaryKey}], [" . implode('], [', $columns) . "]) VALUES (?, " . implode(', ', $placeholders) . ")";
        
        $params = array_merge([$this->getNextId()], array_values($data));
        
        $this->db->execute($sql, $params);
        return $this->db->lastInsertId();
    }
    
    public function update($id, $data) {
        $data = $this->filterFillable($data);
        
        $setClause = [];
        $params = [];
        
        foreach ($data as $column => $value) {
            $setClause[] = "[{$column}] = ?";
            $params[] = $value;
        }
        
        $params[] = $id;
        
        $sql = "UPDATE [dbo].[{$this->table}] SET " . implode(', ', $setClause) . " WHERE [{$this->primaryKey}] = ?";
        
        return $this->db->execute($sql, $params);
    }
    
    public function delete($id) {
        $sql = "DELETE FROM [dbo].[{$this->table}] WHERE [{$this->primaryKey}] = ?";
        return $this->db->execute($sql, [$id]);
    }
    
    public function where($column, $value) {
        $sql = "SELECT * FROM [dbo].[{$this->table}] WHERE [{$column}] = ?";
        return $this->db->fetchAll($sql, [$value]);
    }
    
    public function whereIn($column, $values) {
        if (empty($values)) {
            return [];
        }
        
        $placeholders = str_repeat('?,', count($values) - 1) . '?';
        $sql = "SELECT * FROM [dbo].[{$this->table}] WHERE [{$column}] IN ({$placeholders})";
        
        return $this->db->fetchAll($sql, $values);
    }
    
    protected function filterFillable($data) {
        if (empty($this->fillable)) {
            return $data;
        }
        
        return array_intersect_key($data, array_flip($this->fillable));
    }
    
    protected function getNextId() {
        // For SQL Server with IDENTITY columns, we don't need to specify the ID
        // This method can be overridden for specific tables
        return null;
    }
    
    public function count($conditions = []) {
        $sql = "SELECT COUNT(*) as count FROM [dbo].[{$this->table}]";
        $params = [];
        
        if (!empty($conditions)) {
            $whereClause = [];
            foreach ($conditions as $column => $value) {
                $whereClause[] = "[{$column}] = ?";
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $whereClause);
        }
        
        $result = $this->db->fetchOne($sql, $params);
        return $result['count'];
    }
    
    public function exists($conditions) {
        return $this->count($conditions) > 0;
    }
} 