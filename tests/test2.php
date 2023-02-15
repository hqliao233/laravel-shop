<?php
// 策略模式
interface Number {
    public function number($num);
}
class Add implements Number{
    public function number($num) {
        echo "加法\n" . $num;
    }
}

class Count implements Number{
    public function number($num) {
        echo "计数\n" . $num;
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
$c1 = new Context(new Add());
$c1->get(123);

// 适配器模式
interface Target {
    public function method1();
    public function method2();
}

class Adaptee {
    public function method1() {
        echo "hahaha";
    }
}

class Adapter implements Target {
    private $adaptee;
    public function __construct(Adaptee $adaptee)
    {
        $this->adaptee = $adaptee;
    }

    public function method1() {
        $this->adaptee->method1();
    }

    public function method2() {
        echo "hahahah2";
    }
}

$adapter = new Adapter(new Adaptee);
$adapter->method1();
$adapter->method2();
