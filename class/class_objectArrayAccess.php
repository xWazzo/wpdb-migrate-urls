<?php
/**
* @author Carlos González Front End Ninja
* http://frontend.ninja
*
* Allow objects to work as Array and activate Iterator properties.
*
* Read more:
*   http://de.php.net/manual/en/class.arrayaccess.php 
*   http://ideone.com/rZjim
*   
*/
class ObjectArrayAccess implements ArrayAccess, Iterator {
  private $container = array();

  public function __construct($array) {
      $this->container = $array;
  }

  public function offsetSet($offset, $value) {
      if (is_null($offset)) {
          $this->container[] = $value;
      } else {
          $this->container[$offset] = $value;
      }
  }

  public function offsetExists($offset) {
      return isset($this->container[$offset]);
  }

  public function offsetUnset($offset) {
      unset($this->container[$offset]);
  }

  public function offsetGet($offset) {
      return isset($this->container[$offset]) ? $this->container[$offset] : null;
  }
 
  public function key() {
    return key($this->container);
  }
 
  public function current() {
    return current($this->container);
  }
 
  public function next() {
    next($this->container);
  }
 
  public function rewind() {
    reset($this->container);
  }
 
  public function valid() {
    return current($this->container);
  }
}