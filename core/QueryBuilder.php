<?php

trait QueryBuilder
{

    private $table = '';
    private $where = '';
    private $select = '*';
    private $operator = '';
    private $limit = '';
    private $orderBy = '';
    private $join = '';

    public function table($tableName)
    {
        $this->table = $tableName;

        return $this;
    }

    public function select($fieldSelect = '*')
    {
        $this->select = $fieldSelect;

        return $this;
    }

    public function where($field, $compare, $value)
    {
        if (empty($this->where)) {
            $this->operator = ' WHERE ';
        } else {
            $this->operator = ' AND ';
        }

        $this->where .= " $this->operator $field $compare '$value'";

        return $this;
    }

    public function orWhere($field, $compare, $value)
    {
        if (empty($this->where)) {
            $this->operator = ' WHERE ';
        } else {
            $this->operator = ' OR ';
        }

        $this->where .= " $this->operator $field $compare '$value'";

        return $this;
    }

    public function whereLike($field, $value)
    {
        if (empty($this->where)) {
            $this->operator = ' WHERE ';
        } else {
            $this->operator = ' AND ';
        }

        $this->where .= " $this->operator $field LIKE '%$value%'";

        return $this;
    }

    // limit
    public function limit($number, $offset = 0)
    {
        $this->limit = " LIMIT $offset, $number";

        return $this;
    }

    // order By
    /**
     * ORDER BY id DESC
     * ORDER BY id DESC, name ASC
     */
    public function orderBy($field, $type = 'DESC')
    {
        $fieldArr = explode('/', $field);
        if (!empty($fieldArr) && count($fieldArr) >= 2) {
            $this->orderBY = " ORDER BY " . implode(', ', $fieldArr);
        } else {
            $this->orderBy = " ORDER BY $field $type";
        }

        return $this;
    }

    // inner join
    public function join($tableName, $relationship)
    {
        $this->join = " INNER JOIN $tableName ON $relationship";

        return $this;
    }

    // insert data
    public function insert($data)
    {
        $tableName = $this->table;
        $insertStatus = $this->db->insertData($tableName, $data);

        return $insertStatus;
    }

    // update data 
    public function update($data)
    {
        $whereUpdate = str_replace('WHERE', '', $this->where);
        $whereUpdate = trim($whereUpdate);
        $tableName = $this->table;

        $updateStatus = $this->db->updateData($tableName, $data, $whereUpdate);

        return $updateStatus;
    }

    // delete Data
    public function delete()
    {
        $whereDelete = str_replace('WHERE', '', $this->where);
        $whereDelete = trim($whereDelete);
        $tableName = $this->table;

        $deleteStatus = $this->db->deleteData($tableName, $whereDelete);

        return $deleteStatus;
    }

    // get all data
    public function get()
    {
        $sql = "SELECT $this->select FROM $this->table $this->where";
        $query = $this->query($sql);

        if (!empty($query)) {
            $data = $query->fetchAll(PDO::FETCH_ASSOC);
            return $data;
        }

        // reset field
        $this->table = '';
        $this->select = '*';
        $this->where = '';
        $this->operator = '';

        return false;
    }

    public function first()
    {
        $sql = "SELECT $this->select FROM $this->table $this->where";
        $query = $this->query($sql);

        if (!empty($query)) {
            $data = $query->fetch(PDO::FETCH_ASSOC);
            return $data;
        }

        // reset field
        $this->table = '';
        $this->select = '*';
        $this->where = '';
        $this->operator = '';

        return false;
    }

    public function resetField()
    {
        $this->table = '';
        $this->select = '*';
        $this->where = '';
        $this->operator = '';
        $this->limit = '';
        $this->orderBy = '';
        $this->join = '';
    }
}
