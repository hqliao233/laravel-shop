<?php

//单例
class Connection {
    final private function __construct() {
    }
    private static $instance;
    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }
}

// $rand1 = Connection::getInstance();
// $rand2 = Connection::getInstance();
// var_dump($rand1->rand);
// var_dump($rand2->rand);

// 工厂
class Factory {
    public static function factory($method) {
        switch ($method) {
            case "+" :
                (new Add)->number(123);
                break;
            case "c":
                (new Count)->number(123);
                break;
        }
    }
}
abstract class Number {
    abstract public function number($num);
}
class Add extends Number{
    public function number($num) {
        echo "加法\n";
    }
}

class Count extends Number{
    public function number($num) {
        echo "计数\n";
    }
}
// var_dump(Factory::factory("+"));

class Context {
    private $context;
    public function __construct($num)
    {
        $this->context = $num;
    }
    public function get($num)
    {
        $this->context->number($num);
    }
}
$context1 = new Context(new Add);
$context1->get(123);


class Register {
    static protected $objects;
    static public function set($alies, $object) {
        self::$objects[$alies] = $object;
    }
    static public function get($alies) {
        return self::$objects[$alies];
    }
    static public function unset($alies) {
        unset(self::$objects[$alies]);
    }
}

// Register::set('rand', Factory::factory());
// $regis1 = Register::get('rand');
// var_dump($regis1);

interface Observer {
    public function update();
}

class Action {
    private $_observers = [];
    public function register($name, $object)
    {
        $this->_observers[$name] = $object;
    }
    public function notify() {
        foreach($this->_observers as $observer) {
            $observer->update();
        }
    }
}

class ManObserver implements Observer {
    public function update() {
        echo "man\n";
    }
}

class WomanObserver implements Observer {
    public function update() {
        echo "woman\n";
    }
}
$action = new Action;
$action->register("man", new ManObserver);
$action->register("woman", new WomanObserver);
$action->notify();

