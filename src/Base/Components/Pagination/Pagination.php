<?php

namespace Cubex\Quantum\Base\Components\Pagination;

use Packaged\Glimpse\Tags\Div;
use Packaged\Glimpse\Tags\Link;
use Packaged\Glimpse\Tags\Span;
use Packaged\Ui\Element;
use PackagedUi\Fusion\Fusion;

class Pagination extends Element
{
  protected $_totalItems;
  protected $_itemsPerPage;
  protected $_currentPage;
  protected $_urlCallback;

  public static function create(int $totalItems, int $itemsPerPage, int $currentPage, callable $urlCallback)
  {
    $o = new static();
    $o->_totalItems = $totalItems;
    $o->_itemsPerPage = $itemsPerPage;
    $o->_currentPage = $currentPage;
    $o->_urlCallback = $urlCallback;
    return $o;
  }

  public function render(): string
  {
    $cb = $this->_urlCallback;

    $out = Div::create();

    $totalPages = ceil($this->_totalItems / $this->_itemsPerPage);
    for($i = 0; $i < $totalPages; $i++)
    {
      if($i === $this->_currentPage)
      {
        $link = Span::create($i + 1)->addClass(Fusion::BUTTON, Fusion::BUTTON_DISABLED);
      }
      else
      {
        $link = Link::create($cb($i), $i + 1)->addClass(Fusion::BUTTON);
      }
      $out->appendContent($link);
    }

    return $out;
  }
}
