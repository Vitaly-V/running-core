<?php

namespace Running\tests\Core\TCollection;

use Running\Core\ICollection;
use Running\Core\IObjectAsArray;
use Running\Core\TCollection;

class testClass
    implements ICollection
{
    use TCollection;
}

class Number
{
    protected $data;
    public function __construct($x)
    {
        $this->data = $x;
    }
    public function increment()
    {
        $this->data++;
    }
}


class TCollectionTest extends \PHPUnit_Framework_TestCase
{

    public function testConstruct()
    {
        $collection = new testClass([100, 200, 300, [400, 500]]);

        $this->assertInstanceOf(ICollection::class, $collection);
        $this->assertCount(4, $collection);
        $this->assertEquals(
            [100, 200, 300, new testClass([400, 500])],
            $collection->getData()
        );
        $this->assertEquals(
            [100, 200, 300, [400, 500]],
            $collection->toArray()
        );
        $this->assertEquals(100, $collection[0]);
        $this->assertEquals(200, $collection[1]);
        $this->assertEquals(300, $collection[2]);
        $this->assertEquals(new testClass([400, 500]), $collection[3]);
    }

    public function testAppendPrependAdd()
    {
        $collection = new testClass();

        $collection->append(100);
        $collection->append(200);

        $this->assertCount(2, $collection);
        $this->assertEquals(100, $collection[0]);
        $this->assertEquals(200, $collection[1]);
        $this->assertEquals(
            [100, 200],
            $collection->toArray()
        );

        $collection->prepend(300);

        $this->assertCount(3, $collection);
        $this->assertEquals(300, $collection[0]);
        $this->assertEquals(100, $collection[1]);
        $this->assertEquals(200, $collection[2]);
        $this->assertEquals(
            [300, 100, 200],
            $collection->toArray()
        );

        $collection->add(400);

        $this->assertCount(4, $collection);
        $this->assertEquals(300, $collection[0]);
        $this->assertEquals(100, $collection[1]);
        $this->assertEquals(200, $collection[2]);
        $this->assertEquals(400, $collection[3]);
        $this->assertEquals(
            [300, 100, 200, 400],
            $collection->toArray()
        );
    }

    public function testMerge()
    {
        $collection = new testClass([1, 2]);

        $collection->merge([3, 4]);
        $this->assertCount(4, $collection);
        $expected = new testClass([1, 2, 3, 4]);
        $this->assertEquals($expected->toArray(), $collection->toArray());

        $collection->merge(new testClass([5, 6]));
        $this->assertCount(6, $collection);
        $expected = new testClass([1, 2, 3, 4, 5, 6]);
        $this->assertEquals($expected->toArray(), $collection->toArray());
    }

    public function testSlice()
    {
        $collection = new testClass([10, 20, 30, 40, 50]);
        $this->assertEquals(
            new testClass([30, 40, 50]),
            $collection->slice(2)
        );
        $this->assertEquals(
            new testClass([40, 50]),
            $collection->slice(-2)
        );
        $this->assertEquals(
            new testClass([30, 40]),
            $collection->slice(2, 2)
        );
        $this->assertEquals(
            new testClass([40]),
            $collection->slice(-2, 1)
        );
    }

    public function testFirstLast()
    {
        $collection = new testClass([10, 20, 30, 40, 50]);
        $this->assertEquals(
            10,
            $collection->first()
        );
        $this->assertEquals(
            50,
            $collection->last()
        );
    }

    public function testExistElement()
    {
        $collection = new testClass();
        $el1 = new \Running\Core\Std(['id' => 1, 'title' => 'foo', 'text' => 'FooFooFoo']);
        $collection->append($el1);
        $el2 = new \Running\Core\Std(['id' => 2, 'title' => 'bar', 'text' => 'BarBarBar']);
        $collection->append($el2);
        $collection->append(42);

        $this->assertFalse($collection->existsElement([]));
        $this->assertTrue($collection->existsElement(['id' =>  1]));
        $this->assertFalse($collection->existsElement(['id' =>  3]));
        $this->assertTrue($collection->existsElement(['title' =>  'foo']));
        $this->assertTrue($collection->existsElement(['title' =>  'foo', 'text' => 'FooFooFoo']));
        $this->assertFalse($collection->existsElement(['title' =>  'foo', 'text' => 'BarBarBar']));
    }

    public function testFindAllByAttibutes()
    {
        $collection = new testClass();
        $el1 = new \Running\Core\Std(['id' => 1, 'title' => 'foo', 'text' => 'FooFooFoo']);
        $collection->append($el1);
        $el2 = new \Running\Core\Std(['id' => 2, 'title' => 'foo', 'text' => 'AnotherFoo']);
        $collection->append($el2);
        $collection->append(42);

        $this->assertEquals(
            new testClass([
                new \Running\Core\Std(['id' => 1, 'title' => 'foo', 'text' => 'FooFooFoo']),
                new \Running\Core\Std(['id' => 2, 'title' => 'foo', 'text' => 'AnotherFoo'])
            ]),
            $collection->findAllByAttributes(['title' => 'foo'])
        );
    }

    public function testFindByAttibutes()
    {
        $collection = new testClass();
        $el1 = new \Running\Core\Std(['id' => 1, 'title' => 'foo', 'text' => 'FooFooFoo']);
        $collection->append($el1);
        $el2 = new \Running\Core\Std(['id' => 2, 'title' => 'foo', 'text' => 'AnotherFoo']);
        $collection->append($el2);
        $collection->append(42);

        $this->assertEquals(
            new \Running\Core\Std(['id' => 1, 'title' => 'foo', 'text' => 'FooFooFoo']),
            $collection->findByAttributes(['title' => 'foo'])
        );
    }

    public function testSort()
    {
        $collection = new testClass([10 => 1, 30 => 3, 20 => 2, 'a' => -1, 'b' => 0, 'c' => 42, 1 => '1', '111', '11']);

        $result = $collection->asort();

        $expected = new testClass(['a' => -1, 'b' => 0, 1 => '1', 10 => 1, 20 => 2, 30 => 3, 32 => '11', 'c' => 42, 31 => '111']);
        $this->assertEquals($expected->toArray(), $result->toArray());

        $result = $collection->ksort();

        $expected = new testClass(['a' => -1, 'b' => 0, 'c' => 42, 1 => '1', 10 => 1, 20 => 2, 30 => 3, 31 => '111', 32 => '11']);
        $this->assertEquals($expected->toArray(), $result->toArray());

        $result = $collection->uasort(function ($a, $b) { return $a < $b ? 1 : ($a > $b ? -1 : 0);});

        $expected = new testClass([31 => '111', 'c' => 42, 32 => '11', 30 => 3, 20 => 2, 10 => 1, 1 => '1', 'b' => 0, 'a' => -1]);
        $this->assertEquals($expected->toArray(), $result->toArray());

        $result = $collection->sort(function ($a, $b) { return -($a <=> $b);});

        $expected = new testClass([31 => '111', 'c' => 42, 32 => '11', 30 => 3, 20 => 2, 10 => 1, 1 => '1', 'b' => 0, 'a' => -1]);
        $this->assertEquals($expected->toArray(), $result->toArray());

        $result = $collection->uksort(function ($a, $b) { return $a < $b ? 1 : ($a > $b ? -1 : 0);});

        $expected = new testClass([32 => '11', 31 => '111', 30 => 3, 20 => 2, 10 => 1, 1 => '1', 'c' => 42, 'b' => 0, 'a' => -1]);
        $this->assertEquals($expected->toArray(), $result->toArray());

        $collection = new testClass([0 => '12', 1 => '10', 2 => '2', 3 => '1']);
        $result = $collection->natsort();
        $expected = new testClass([3 => '1', 2 => '2', 1 => '10', 0 => '12']);
        $this->assertEquals($expected->toArray(), $result->toArray());

        $collection = new testClass([0 => 'IMG0.png', 1 => 'img12.png', 2 => 'img10.png', 3 => 'img2.png', 4 => 'img1.png', 5 => 'IMG3.png']);
        $result = $collection->natcasesort();
        $expected = new testClass([0 => 'IMG0.png', 4 => 'img1.png', 3 => 'img2.png',  5 => 'IMG3.png', 2 => 'img10.png', 1 => 'img12.png']);
        $this->assertEquals($expected->toArray(), $result->toArray());
    }

    public function testReverse()
    {
        $collection = new testClass([10 => 1, 30 => 3, 20 => 2, 'a' => -1, 'b' => 0, 'c' => 42, '111', '11']);
        $result = $collection->reverse();

        $expected = new testClass([32 => '11', 31 => '111', 'c' => 42, 'b' => 0, 'a' => -1, 20 => 2, 30 => 3, 10 => 1]);
        $this->assertEquals($expected->toArray(), $result->toArray());
    }

    public function testMap()
    {
        $collection = new testClass([1, 2, 3]);
        $result = $collection->map(function ($x) {return $x*2;});

        $expected = new testClass([2, 4, 6]);
        $this->assertEquals(array_values($expected->toArray()), array_values($result->toArray()));
    }

    public function testFilter()
    {
        $collection = new testClass([1, -1, 0, 2, 3, -5]);
        $result = $collection->filter(function ($x) {return $x>0;});

        $expected = new testClass([1, 2, 3]);
        $this->assertEquals($expected->toArray(), $result->toArray());
    }

    public function testReduce()
    {
        $collection = new testClass([1, 2, 3, 4]);
        $reduced = $collection->reduce(0, function($carry, $item) {
            return $carry + $item;
        });
        $this->assertEquals(10, $reduced);
    }

    public function testCollect()
    {
        $i1 = new \Running\Core\Std(['id' => 1, 'title' => 'foo']);
        $i2 = new \Running\Core\Std(['id' => 2, 'title' => 'bar']);
        $i3 = (object)['id' => 3, 'title' => 'baz'];

        $collection = new testClass();
        $collection->append($i1);
        $collection->append($i2);
        $collection->append($i3);

        $this->assertEquals(
            [
                new \Running\Core\Std(['id' => 1, 'title' => 'foo']),
                new \Running\Core\Std(['id' => 2, 'title' => 'bar']),
                (object)['id' => 3, 'title' => 'baz']
            ],
            $collection->toArray()
        );

        $ids = $collection->collect('id');
        $this->assertEquals([1, 2, 3], $ids);

        $titles = $collection->collect(function ($x) {
            return $x->title;
        });
        $this->assertEquals(['foo', 'bar', 'baz'], $titles);

        $collection = new testClass([
            ['id' => 1, 'title' => 'foo'],
            ['id' => 2, 'title' => 'bar'],
            ['id' => 3, 'title' => 'baz'],
        ]);

        $ids = $collection->collect('id');
        $this->assertEquals([1, 2, 3], $ids);

        $titles = $collection->collect(function ($x) {
            return $x['title'];
        });
        $this->assertEquals(['foo', 'bar', 'baz'], $titles);
    }

    public function testGroup()
    {
        $collection = new testClass([
            ['date' => '2000-01-01', 'title' => 'First'],
            ['date' => '2000-01-01', 'title' => 'Second'],
            ['date' => '2000-01-02', 'title' => 'Third'],
            (object)['date' => '2000-01-04', 'title' => 'Fourth'],
        ]);

        $grouped = $collection->group('date');
        $this->assertEquals([
            '2000-01-01' => new testClass([['date' => '2000-01-01', 'title' => 'First'], ['date' => '2000-01-01', 'title' => 'Second']]),
            '2000-01-02' => new testClass([['date' => '2000-01-02', 'title' => 'Third']]),
            '2000-01-04' => new testClass([(object)['date' => '2000-01-04', 'title' => 'Fourth']]),
        ], $grouped);

        $grouped = $collection->group(function ($x) {return date('m-d', strtotime($x instanceof IObjectAsArray ? $x['date'] : $x->date));});
        $this->assertEquals([
            '01-01' => new testClass([['date' => '2000-01-01', 'title' => 'First'], ['date' => '2000-01-01', 'title' => 'Second']]),
            '01-02' => new testClass([['date' => '2000-01-02', 'title' => 'Third']]),
            '01-04' => new testClass([(object)['date' => '2000-01-04', 'title' => 'Fourth']]),
        ], $grouped);
    }

    public function testCall()
    {
        $collection = new testClass();
        $collection->append(new Number(1));
        $collection->append(new Number(2));
        $collection->append(new Number(3));

        $collectionExpected = new testClass();
        $collectionExpected->append(new Number(2));
        $collectionExpected->append(new Number(3));
        $collectionExpected->append(new Number(4));

        $collection->increment();
        $this->assertEquals($collectionExpected, $collection);
    }

}