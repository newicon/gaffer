<?php declare(strict_types=1);

namespace Gaffer\Db;

use Rakit\Validation\Validator;

/**
 * basic db model : represents a row, no join support!
 */
abstract class Model implements \ArrayAccess
{
    /**
     * @readonly
     * @primarykey
     * @autoincrement
     */
    public ?int $id = null;

    /** @var array table name by class (calculated by convention) */
    private static array $tableNameByClass = [];

    /** @var array  */
    private static array $phpTypesByPropertyByClass = [];

    /** @var array  */
    private static array $sqlTypeByPropertyByClass = [];

    /** @var array all property annotations */
    private static array $annotationsByPropertyByClass = [];

    /** @var array which properties should be persisted to the db */
    private static array $persistPropertiesByClass = [];

    /** @var array validation rules (as per Rakit/Validation) */
    private static array $validationRulesByClass = [];

    /** @var array any additional data */
    private array $extraData = [];

    /**
     * ensure meta-data
     * this could all be cached as it rarely changes!
     */
    private static function init()
    {
        $class = get_called_class();
        if (!isset(self::$persistPropertiesByClass[$class])) {
            $annotationsByProperty = \Gaffer\Util::getPropertyAnnotations($class);
            self::$annotationsByPropertyByClass[$class] = $annotationsByProperty;
            self::$persistPropertiesByClass[$class] = [];
            self::$validationRulesByClass[$class] = [];
            foreach ($annotationsByProperty as $property=>$annotations) {
                foreach ($annotations as $annotation) {
                    if ($annotation === 'persist') {
                        self::$persistPropertiesByClass[$class][] = $property;
                    } elseif (strpos($annotation,'validation ')===0) {
                        self::$validationRulesByClass[$class][$property] = substr($annotation,11);
                    }
                }
            }
        }
        if (!isset(self::$tableNameByClass[$class])) {
            $reflect = new \ReflectionClass($class);
            self::$tableNameByClass[$class] =  \Gaffer\Util::camelCaseToSnakeCase($reflect->getShortName());
            $properties = $reflect->getProperties();
            foreach ($properties as $property) {
                $propertyName = $property->getName();
                $reflectionProperty = new \ReflectionProperty($class, $propertyName);
                $type =  $reflectionProperty->getType();
                self::$phpTypesByPropertyByClass[$class][$propertyName]  = $type ? $type->getName() : null;
            }
        }
        if (!isset(self::$sqlTypeByPropertyByClass[$class]))
        {
            $results = DB::all("DESCRIBE `".self::$tableNameByClass[$class]."`");
            foreach ($results as $result) {
                self::$sqlTypeByPropertyByClass[$class][$result['Field']] = $result['Type'];
            }
        }
    }

    /**
     * @return string
     */
    public static function getTable(): string
    {
        $class = get_called_class();
        if (!isset(self::$tableNameByClass[$class])) {
            $reflect = new \ReflectionClass(get_called_class());
            self::$tableNameByClass[$class] = \Gaffer\Util::camelCaseToSnakeCase($reflect->getShortName());
        }
        return self::$tableNameByClass[$class];
    }

    /**
     * @return int the id of the saved row
     * @throws \Exception thrown if the update or save failed
     */
    public function save(): int
    {
        $properties = self::getPersistedFields();
        $allData = get_object_vars($this);
        $data = array_intersect_key($allData, array_flip($properties));
        $table = self::getTable();
        if (isset($allData['id']) && $allData['id']) {
            if (!is_int($allData['id'])) {
                $allData['id'] = filter_var($allData['id'], FILTER_VALIDATE_INT);
                if ($allData['id']===false) {
                    throw new \Exception("'id' could not be converted to an int : ".print_r($allData['id'],true));
                }
            }
            if ($allData['id']<1) {
                throw new \Exception("'id' cannot be 0 or negative");
            }
            // sanity check ... does the specified id actually exist?
            // ids should only be initially populated via auto-increment, but it should be possible to manipulate a
            // model via an existing one
            $existingId = DB::scalar("SELECT id FROM `" . $table . "` WHERE `id`=:id", ['id' => $allData['id']]);
        }
        else {
            $existingId = null;
        }

        foreach($data as $field=>&$value) {
            $value = $this->castPropertyValueForSql($field, $value);
        }

        if (!$existingId) {
            $id = DB::insert($table, $data);
            $this->id = $id;
            return (int)$this->id;
        }
        else {
            $id = $existingId;
            unset($data['id']);
            DB::update($table, $data, "`id`=:id", ['id'=>$id]);
            return (int)$id;
        }
    }

    /**
     * @param array|null $data
     * @return array|bool
     */
    public function validate(array $data=null): array|bool
    {
        self::init();
        if ($data==null) {
            $data = get_object_vars($this);
        }
        $validator = new Validator();
        $validation = $validator->validate($data, self::$validationRulesByClass[get_called_class()]);
        if ($validation->fails()) {
            return $validation->errors()->toArray();
        }
        else {
            return true;
        }
    }

    /**
     * @param string|null $class
     * @return array
     */
    public static function getPersistedFields(string $class=null): array
    {
        self::init();
        return self::$persistPropertiesByClass[$class ?: get_called_class()];
    }

    /**
     * @param string $where
     * @param array $params
     * @return Model|null
     * @throws \Exception
     */
    public static function hydrateOne(string $where, array $params): ?Model
    {
        $class = get_called_class();
        $properties = self::getPersistedFields();
        array_unshift($properties,'id');// id is a special case
        if (count($properties)===0) {
            throw new \Exception("No properties are defined for the model : ".$class);
        }
        $sql = "SELECT `".implode("`,`", $properties)."` FROM ".self::getTable()." WHERE ".$where." LIMIT 1";
        $data = DB::one($sql, $params);
        if (!$data) {
            return null;
        }
        $object = new $class();
        foreach ($properties as $property) {
            $object->$property = self::castPropertyValueForPhp($property, $data[$property]);
        }
        return $object;
    }

    /**
     * @param null|string $where
     * @param null|array $params
     * @param string|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     * @throws \Exception
     */
    public static function hydrateMany(string $where=null, array $params=null, string $orderBy=null, int $limit=null, int $offset=null): array
    {
        $class = get_called_class();
        $properties = self::getPersistedFields();
        array_unshift($properties,'id');// id is a special case
        if (count($properties)===0) {
            throw new \Exception("No properties are defined for the model : ".$class);
        }
        $sql = "SELECT `".implode("`,`", $properties)."` FROM `".self::getTable()."`";
        if ($where) {
            $sql.=" WHERE ".$where;
        }
        if ($orderBy) {
            $sql.=" ORDER BY ".$orderBy;
        }
        if ($limit) {
            $sql.=" LIMIT ".$limit;
            if ($offset) {
                $sql.=" OFFSET ".$offset;
            }
        }
        $data = DB::all($sql, $params);
        $objects = [];
        if (!$data) {
            return $objects;
        }

        foreach ($data as $datum) {
            $object = new $class();
            foreach ($properties as $property)
            {
                $object->$property = self::castPropertyValueForPhp($property, $datum[$property]);
            }
            $objects[] = $object;
        }
        return $objects;
    }

    /**
     * @param string $columnName
     * @param string|null $where
     * @param array|null $params
     * @param string|null $indexBy
     * @return array
     * @throws \Exception
     */
    public static function column(string $columnName, string $where=null, array $params=null, string $indexBy=null ): array
    {
        $class = get_called_class();
        $properties = self::getPersistedFields();
        if (!in_array($columnName, $properties)) {
            throw new \Exception("'".$columnName."' is not a registered field in ".$class);
        }
        if (!$indexBy) {
            $sql = "SELECT `'.$columnName.'` FROM `".self::getTable()."`";
            if ($where) {
                $sql.=" WHERE ".$where;
            }
            return Db::column($sql, $params);
        }
        else {
            $sql = "SELECT `".$columnName."`,`".$indexBy."` FROM `".self::getTable()."`";
            if ($where) {
                $sql.=" WHERE ".$where;
            }
            $results = Db::all($sql, $params);
            return array_column($results, $columnName, $indexBy);
        }
    }

    /**
     * @param string $field
     * @param mixed $value
     * @return bool|float|int|string
     */
    private static function castPropertyValueForPhp(string $field, $value)
    {
        $class = get_called_class();
        $castTo = self::$phpTypesByPropertyByClass[$class][$field] ?? null;
        switch($castTo) {
            case "int":
                return is_int($value) ? $value : (int)$value;
            case "float":
                return is_float($value) ? $value : (float)$value;
            case "boolean":
            case "bool":
                return is_bool($value) ? $value : (bool)$value;
            case "string":
            default:
                return $value;
        }
    }

    /**
     * @param string $field
     * @param mixed $value
     * @return mixed
     * @throws \Exception
     */
    private static function castPropertyValueForSql(string $field, $value)
    {
        $class = get_called_class();
        $castTo = self::$sqlTypeByPropertyByClass[$class][$field] ?? null;
        $openBracketPos = strpos($castTo,'(');
        if ($openBracketPos!==FALSE) {
            $castToType = substr($castTo,0,$openBracketPos);
            //$closeBracketPos = strpos($castTo,')',$openBracketPos+1);
            //$castToParam = substr($castTo, $openBracketPos+1, ($closeBracketPos-$openBracketPos-1));
        }
        else {
            $castToType = $castTo;
            //$castToParam = null;
        }
        if (!$castTo) {
            return $value;
        }
        switch($castToType) {
            // string types
            case 'char':
            case 'varchar':
            case 'text':
            case 'set':
            case 'enum':
                return (string)$value;

            // number types
            case 'bit':
            case 'tinyint':
            case 'int':
                return is_int($value) ? $value : intval($value);
            case 'bool':
            case 'boolean':
                return $value ? 1 : 0;

            // date and time
            case 'date':
                return $value instanceof \DateTime ? $value->format("Y-m-d") : ($value ? (string)$value : null);
            case 'timestamp':
            case 'datetime':
                return $value instanceof \DateTime ? $value->format("Y-m-d H:i:s") : ($value ? (string)$value : null);
            case 'time':
                return $value instanceof \DateTime ? $value->format("H:i:s") : ($value ? (string)$value : null);
            case 'year':
                return $value instanceof \DateTime ? $value->format("o") : ($value ? (string)$value : null);

            default:
                throw new \Exception("Mysql type '".$castToType."' is not yet supported");
        }
    }

    /**
     * @param array $data
     * @param bool $strict
     * @return bool
     * @throws \Exception
     */
    public function populate(array $data, bool $strict=false): bool
    {
        $class = get_called_class();
        $fields = self::getPersistedFields();
        foreach ($data as $key=>$value) {
            if (!in_array($key, $fields)) {
                if ($strict) {
                    throw new \Exception($class . " does not have the field '" . $key . "'");
                }
            }
            else {
                $this->{$key} = self::castPropertyValueForPhp($key,$value);
            }
        }
        return true;
    }

    public function __set($name, $value)
    {
        $this->extraData[$name] = $value;
    }

    public function __get($name)
    {
        return $this->extraData[$name] ?? null;
    }

    public function __isset($name)
    {
        return isset($this->extraData);
    }

    public function __unset($name)
    {
        unset($this->extraData[$name]);
    }

    public function offsetExists($offset): bool
    {
        $data = get_object_vars($this);
        return isset($data[$offset]);
    }

    public function offsetGet($offset): mixed
    {
        $data = get_object_vars($this);
        if (isset($data[$offset])) {
            return $data[$offset];
        }
        return $this->extraData[$offset] ?? null;
    }

    public function offsetSet($offset, $value): void
    {
        $data = get_object_vars($this);
        if (isset($data[$offset]) && property_exists($this,$offset)) {
            $this->{$offset} = $value;
        }
        else {
            $this->extraData[$offset] = $value;
        }
    }

    public function offsetUnset($offset): void
    {
        $data = get_object_vars($this);
        if (isset($data[$offset])) {
            $this->{$offset} = null;
        }
        elseif (isset($this->extraData[$offset])) {
            unset($this->extraData[$offset]);
        }
    }

    /**
     * @return array
     */
    public function getPersistedDataArray(): array
    {
        $data = get_object_vars($this);
        $persistedDataArray = array_intersect_key($data, array_flip(self::getPersistedFields()));
        if (isset($data['id']) && $data['id']) {
            $persistedDataArray['id'] = $data['id'];
        }
        return $persistedDataArray;
    }
}

