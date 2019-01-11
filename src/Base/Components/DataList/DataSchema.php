<?php
namespace Cubex\Quantum\Base\Components\DataList;

class DataSchema
{
  /**
   * @var array
   */
  protected $_data;

  /**
   * @var DataFieldSchema[]
   */
  protected $_fields = [];

  public static function create(array $data)
  {
    $o = new static;
    $o->_data = $data;
    return $o;
  }

  public function getData()
  {
    return $this->_data;
  }

  public function addField(DataFieldSchema $field)
  {
    $this->_fields[] = $field;
    return $this;
  }

  public function getFields()
  {
    return $this->_fields;
  }
}
