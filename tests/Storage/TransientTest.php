<?php 

use InnoCraft\Experiments\Storage\Transient;

class TransientTest extends PHPUnit_Framework_TestCase {

    /**
     * @var Transient
     */
    private $transient;

    public function setUp()
    {
        $this->transient = new Transient();
    }

    public function test_get_shouldReturnNull_IfNoValueIsSet()
    {
        $this->assertNull($this->transient->get('mynamespace', 'mykey'));
    }

    public function test_set_shouldSavesValueUnderGivenNamespace()
    {
        $this->transient->set('mynamespace', 'mykey', 'myValue');

        $this->assertSame('myValue', $this->transient->get('mynamespace', 'mykey'));
        $this->assertNull($this->transient->get('otherNamespace', 'mykey'));
        $this->assertNull($this->transient->get('mynamespace', 'otherKey'));
    }

    public function test_set_canSaveManyDifferentKeysAndNamespaces()
    {
        $this->transient->set('mynamespace', 'mykey', 'myValue1');
        $this->transient->set('mynamespace', 'mykey1', 'myValue2');
        $this->transient->set('mynamespace1', 'mykey', 'myValue3');

        $this->assertSame('myValue1', $this->transient->get('mynamespace', 'mykey'));
        $this->assertSame('myValue2', $this->transient->get('mynamespace', 'mykey1'));
        $this->assertSame('myValue3', $this->transient->get('mynamespace1', 'mykey'));
    }

    public function test_set_overwritesValue_IfAlreadySet()
    {
        $this->transient->set('mynamespace', 'mykey', 'myValue');
        $this->assertSame('myValue', $this->transient->get('mynamespace', 'mykey'));

        $this->transient->set('mynamespace', 'mykey', 'myValue2');
        $this->assertSame('myValue2', $this->transient->get('mynamespace', 'mykey'));
    }
}