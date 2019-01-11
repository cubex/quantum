<?php
namespace Cubex\Quantum\Base\Components\DataList;

use Cubex\Ui\UiElement;
use Packaged\Dispatch\Component\DispatchableComponent;
use Packaged\Dispatch\ResourceManager;
use Packaged\Glimpse\Tags\Table\Table;
use Packaged\Glimpse\Tags\Table\TableBody;
use Packaged\Glimpse\Tags\Table\TableCell;
use Packaged\Glimpse\Tags\Table\TableHead;
use Packaged\Glimpse\Tags\Table\TableHeading;
use Packaged\Glimpse\Tags\Table\TableRow;

/**
 * Pass a DAO collection and render a table
 */
class DataList extends UiElement implements DispatchableComponent
{
  /** @var DataSchema */
  protected $_schema;

  public static function create(DataSchema $schema)
  {
    $o = new static;
    $o->_schema = $schema;
    return $o;
  }

  public function render(): string
  {
    ResourceManager::component($this)->requireCss('datalist.css');
    $table = Table::create()->addClass('datalist');

    $head = new TableHead();
    $table->appendContent($head);
    foreach($this->_schema->getFields() as $field)
    {
      $head->appendContent(TableHeading::create($field->text));
    }

    $body = TableBody::create();
    $table->appendContent($body);
    /** @var array $data */
    foreach($this->_schema->getData() as $data)
    {
      $row = TableRow::create();
      $body->appendContent($row);
      foreach($this->_schema->getFields() as $field)
      {
        $value = $data;
        $list = explode('.', $field->property);
        while($k = array_shift($list))
        {
          $value = $value[$k];
        }
        $row->appendContent(TableCell::create($value ?: $field->defaultValue)->addClass($field->getClass()));
      }
    }
    return $table;
  }

  public function getResourceDirectory()
  {
    return __DIR__ . '/resources';
  }
}
