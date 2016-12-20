<?php 

use InnoCraft\Experiments\Storage\Cookie;

class CookieTest extends PHPUnit_Framework_TestCase {

    /**
     * @var Cookie
     */
    private $cookie;

    private $namespace = 'mynamespace';
    private $key = 'mykey';

    public function setUp()
    {
        $this->cookie = new Cookie();
    }

    public function test_get_shouldReturnNull_IfNoValueIsSet()
    {
        $this->assertNull($this->getValue());
    }

    public function test_set_shouldSavesValueUnderGivenNamespace()
    {
        $this->setValue('myValue');

        $this->assertSame('myValue', $this->getValue());
        $this->assertNull($this->cookie->get('otherNamespace', $this->key));
        $this->assertNull($this->cookie->get($this->namespace, 'otherKey'));
    }

    public function test_set_shouldSavesValueAcrossInstances()
    {
        $this->assertSame('myValue', $this->getValue());
    }

    public function test_set_overwritesValue_IfAlreadySet()
    {
        $this->setValue('myValue3');
        $this->assertSame('myValue3', $this->getValue());

        $this->setValue('myValue4');
        $this->assertSame('myValue4', $this->getValue());
    }

    public function test_get_reusesAPreviouslyStoredCookieValue()
    {
        $_COOKIE = array($this->namespace . '_mycookie' => 'myValue999');
        $this->assertSame('myValue999', $this->cookie->get($this->namespace, 'mycookie'));
    }

    private function setValue($value)
    {
        $this->cookie->set($this->namespace, $this->key, $value);
    }

    private function getValue()
    {
        return $this->cookie->get($this->namespace, $this->key);
    }
}