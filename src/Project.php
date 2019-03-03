<?php
namespace Cubex\Quantum;

use Cubex\Quantum\Base\QuantumProject;
use Packaged\Dispatch\ResourceManager;
use PackagedUi\Fusion\Fusion;

class Project extends QuantumProject
{
  protected function _init()
  {
    Fusion::includeGoogleFont();
    $rm = ResourceManager::component(new Fusion());
    $rm->requireJs(Fusion::FILE_BASE_JS);
    $rm->requireCss(Fusion::FILE_BASE_CSS);
    ResourceManager::vendor('packaged-ui', 'fontawesome')->requireCss('assets/css/all.min.css');

    //$this->addModule();
    //$this->removeModule();
  }
}
