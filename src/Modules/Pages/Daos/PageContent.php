<?php
namespace Cubex\Quantum\Modules\Pages\Daos;

use Cubex\Quantum\Base\Daos\QuantumQlDao;

class PageContent extends QuantumQlDao
{
  public $pageId;

  public $title;
  public $content;

  public $theme = '';

  public function getDaoIDProperties()
  {
    return ['pageId', 'id'];
  }

  public function save()
  {
    if($this->hasChanges())
    {
      $this->id = null;
      $this->markDaoAsLoaded(false);
    }
    return parent::save();
  }
}
