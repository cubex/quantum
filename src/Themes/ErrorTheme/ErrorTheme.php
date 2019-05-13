<?php
namespace Cubex\Quantum\Themes\ErrorTheme;

use Cubex\Quantum\Themes\BaseTheme;

class ErrorTheme extends BaseTheme
{
  protected $_code;
  protected $_title = 'Something went wrong';

  /**
   * @return mixed
   */
  public function getCode()
  {
    return $this->_code;
  }

  /**
   * @param mixed $code
   *
   * @return $this
   */
  public function setCode($code)
  {
    $this->_code = $code;
    return $this;
  }

  /**
   * @return mixed
   */
  public function getTitle()
  {
    switch($this->_code)
    {
      case 404:
        return 'Page Not Found';
      case 500:
        return 'We broke something';
    }
    return $this->_title;
  }

  /**
   * @param mixed $title
   *
   * @return $this
   */
  public function setTitle($title)
  {
    $this->_title = $title;
    return $this;
  }
}
