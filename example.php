<?php
/**
 * EX_1_1: type declarations & return type
 */
function str2Int(string $string): int
{
    return (int)$string
}





/**
 * EX_1_2: define array constant
 */
define('ANIMALS', ['dog', 'cat', 'bird']);





/**
 * EX_1_3: intdiv()
 */
echo 7 / 2; // output: 3.5
// improvement:
echo floor(7 / 2); // output: 3



echo floor(7 / -2); // output: -4
// improvement:
$result = 7 / -2;
echo $result > 0 ? floor($result) : ceil($result); // output: -3



// PHP 7+
echo intdiv(7, 2);  // output: 3
echo intdiv(7, -2); // output: -3





/**
 * EX_2_1: trait
 */
trait ExampleTrait
{
    public $var = 'var';
    public function traitFunc()
    {
        return 'test';
    }
}

class TraitExampleClass
{
    use ExampleTrait;
    public function callTrait()
    {
        echo $this->var.'/'.traitFunc();
    }
}

$obj = new TraitExampleClass;
$obj->callTrait(); // output: var/test




/**
 * EX_3_1: call closure
 */
$func = function ($a, $b) {
    return $a + $b;
};

echo $func(1, 2); // output: 3





/**
 * EX_3_2: call closure with 'use'
 */
$func = function ($a, $b) {
    return $a + $b;
};

$sumFirstAndLast = function (array $arr) use ($func) {
    if (empty($arr)) return 0;
    return $func(array_shift($arr), array_pop($arr));
};

$sumFirstAndLast([1, 2, 3, 4, 5]); // output: 6





/**
 * EX_3_3: call closure recursively
 */
$factorial = function($n) use (&$factorial) {
    if ($n <= 1) return 1;
    return $factorial($n - 1) * $n;
};

echo $factorial(5); // output: 120





/**
 * EX_4_1: error handler sample setting
 */
set_error_handler(function ($level, $message, $file, $line) {
    // 以下寫法會傳球給 set_exception_handler()，但是會中斷程式執行
    throw new ErrorException($message, 0, $level, $file, $line);
});

set_exception_handler(function ($exception) {
    // 1. 發生例外時會中斷程式執行
    //     => 處理例外結束後會直接進到 register_shutdown_function()
    // 2. 原本發生例外時 error_get_last() 會回傳 type 為 E_ERROR 的紀錄
    //     => 這裡會把紀錄吃掉，所以 register_shutdown_function() 裡面 error_get_last() 會回傳 null
    echo 'uncaught: '.$exception->getMessage()."\n";
});

register_shutdown_function(function () {
    $last_error = error_get_last();
    $report_level = [E_ERROR, E_PARSE, E_CORE_ERROR, E_CORE_WARNING, E_COMPILE_ERROR, E_COMPILE_WARNING];
    if ($last_error && in_array($last_error['type'], $report_level)) {
        // do something
    }
    // do finally things
});

try {
    throw new Exception('first');
} catch (Exception $e) {
    echo 'catch: '.$e->getMessage()."\n";
}
echo "go through try-catch block\n";

throw new Exception('second');
echo "end\n";

// output:
// catch: first
// go through try-catch block
// uncaught: second





/**
 * EX_4_2: 7.0+ error handler sample setting
 */
set_error_handler(function ($level, $message, $file, $line) {
    // 以下寫法會傳球給 set_exception_handler()，但是會中斷程式執行
    throw new ErrorException($message, 0, $level, $file, $line);
});

set_exception_handler(function ($error) {
    // 1. 發生例外時會中斷程式執行
    //     => 處理例外結束後會直接進到 register_shutdown_function()
    // 2. 原本發生例外時 error_get_last() 會回傳 type 為 E_ERROR 的紀錄
    //     => 這裡會把紀錄吃掉，所以 register_shutdown_function() 裡面 error_get_last() 會回傳 null
    if ($error instanceof Error) {
        echo 'fatal error: '.$error->getMessage()."\n";
    } else {
        echo 'uncaught exception: '.$error->getMessage()."\n";
    }
});

register_shutdown_function(function () {
    // do finally things
});





/**
 * EX_5_1: __invoke()
 */
class MagicInvoke
{
    public $num = 1;
    public function __invoke($input)
    {
        return $this->num + $input;
    }
}
$obj = new MagicInvoke;
echo $obj(2); // output: 3





/**
 * EX_5_2: __toString()
 */
class MagicToString
{
    public $prefix = 'MTS_';
    public $str;
    public function __toString()
    {
        return $this->prefix.$this->str;
    }
}
$obj = new MagicToString;
echo $obj; // output: MTS_
$obj->str = 'value';
echo $obj; // output: MTS_value





/**
 * EX_5_3: __debugInfo()
 */
class MagicDebugInfo
{
    function __debugInfo()
    {
        return [1, 2, 3];
    }
}
$obj = new MagicDebugInfo;
var_dump($obj);

// output:
// object(MagicDebugInfo)#1 (3) {
//   [0]=>
//   int(1)
//   [1]=>
//   int(2)
//   [2]=>
//   int(3)
// }





/**
 * EX_X_1: loop query
 */
$order = [
    11111 => ['pid' => 123],
    22222 => ['pid' => 123],
    33333 => ['pid' => 124]
];
foreach ($order as $bill_no => $data) {
    $pid = $data['pid'];
    $sql = "SELECT * FROM products WHERE product_id = $pid;";
    $result = $db->getAll($sql);
    $order[$bill_no]['prod_info'] = $result;
}



// improvement
$order = [
    11111 => ['pid' => 123],
    22222 => ['pid' => 123],
    33333 => ['pid' => 124]
];
$prod_info_map = [];
$pid_array = array_unique(array_column($order, 'pid'));
if (count($pid_array) > 0) {
    $list = implode(',', $pid_array);
    $sql = "SELECT * FROM products WHERE product_id IN ($list);";
    $result = $db->getAll($sql);
    $prod_info_map = array_combine(
        array_column($result, 'product_id'),
        $result
    );
}





/**
 * EX_X_2: singleton
 */
class NonSingletonValidator
{
    public $mode = 'default';
}

class NonSingletonClass
{
    public $validator;
    public function __construct()
    {
        $this->validator = new NonSingletonValidator;
    }
}

$a = new NonSingletonClass;
$b = new NonSingletonClass;

$a->validator->mode = 'A';
$b->validator->mode = 'B';

echo $a->validator->mode; // output: 'A'
echo $b->validator->mode; // output: 'B'



// improvement
class SingletonValidator
{
    public $mode = 'default';
}

class SingletonClass
{
    public static $validator;
    public function getValidator()
    {
        if (!isset(self::$validator)) {
            self::$validator = new SingletonValidator;
        }
        return self::$validator;
    }
}

$a = new SingletonClass;
$b = new SingletonClass;

$a->getValidator()->mode = 'A';
$b->getValidator()->mode = 'B';

echo $a->getValidator()->mode; // output: 'B'
echo $b->getValidator()->mode; // output: 'B'





/**
 * EX_X_3: validate numeric string
 */
$var = '1234';
echo (string)$var === (string)(int)$var ? 'Y' : 'N'; // output: Y
$var = '1234.5';
echo (string)$var === (string)(int)$var ? 'Y' : 'N'; // output: N
$var = '0987654321';
echo (string)$var === (string)(int)$var ? 'Y' : 'N'; // output: N
$var = '0987654321.';
echo (string)$var === (string)(int)$var ? 'Y' : 'N'; // output: N
// 適用純整數



$var = '1234';
echo (string)$var === (string)(float)$var ? 'Y' : 'N'; // output: Y
$var = '1234.5';
echo (string)$var === (string)(float)$var ? 'Y' : 'N'; // output: Y
$var = '0987654321';
echo (string)$var === (string)(float)$var ? 'Y' : 'N'; // output: N
$var = '0987654321.';
echo (string)$var === (string)(float)$var ? 'Y' : 'N'; // output: N
// 適用純數值



$pattern = '/^[0-9]+$/';
echo preg_match($pattern, '1234')        ? 'Y' : 'N'; // output: Y
echo preg_match($pattern, '1234.5')      ? 'Y' : 'N'; // output: N
echo preg_match($pattern, '0987654321')  ? 'Y' : 'N'; // output: Y
echo preg_match($pattern, '0987654321.') ? 'Y' : 'N'; // output: N

$pattern = '/^[0-9]+\.?[0-9]+$/';
echo preg_match($pattern, '1234')         ? 'Y' : 'N'; // output: Y
echo preg_match($pattern, '1234.5')       ? 'Y' : 'N'; // output: Y
echo preg_match($pattern, '0987654321')   ? 'Y' : 'N'; // output: Y
echo preg_match($pattern, '0987654321.')  ? 'Y' : 'N'; // output: N
// 萬用，但是效能沒有很好，而且正規表示式門檻相對高



echo ctype_digit('1234')         ? 'Y' : 'N'; // output: Y
echo ctype_digit('1234.5')       ? 'Y' : 'N'; // output: N
echo ctype_digit('0987654321')   ? 'Y' : 'N'; // output: Y
echo ctype_digit('0987654321.')  ? 'Y' : 'N'; // output: N
echo ctype_digit(1234)           ? 'Y' : 'N'; // output: Y
echo ctype_digit(1234.5)         ? 'Y' : 'N'; // output: N
echo ctype_digit(12)             ? 'Y' : 'N'; // output: N
// 能不用就不用



echo ctype_alnum('a1b2c3d4e5')   ? 'Y' : 'N'; // output: N
echo ctype_alnum('1234')         ? 'Y' : 'N'; // output: Y
echo ctype_alnum('1234.5')       ? 'Y' : 'N'; // output: N
echo ctype_alnum('0987654321')   ? 'Y' : 'N'; // output: Y
echo ctype_alnum('0987654321.')  ? 'Y' : 'N'; // output: N
echo ctype_alnum(1234)           ? 'Y' : 'N'; // output: Y
echo ctype_alnum(1234.5)         ? 'Y' : 'N'; // output: N
echo ctype_alnum(12)             ? 'Y' : 'N'; // output: N
// 能不用就不用



echo is_numeric('1.')            ? 'Y' : 'N'; // output: Y
echo is_numeric('0x539')         ? 'Y' : 'N'; // output: Y
echo is_numeric('0b10100111001') ? 'Y' : 'N'; // output: Y
echo is_numeric('1337e0')        ? 'Y' : 'N'; // output: Y
echo is_numeric('0123')          ? 'Y' : 'N'; // output: Y
echo 0123;                                    // output: 83
// 能不用就不用