<?php
function sort_two($arr) {
    $length = count($arr);
    if ($length <= 1) {
        return $arr;
    }
    $first = $arr[0];
    $left_array = $right_array = [];
    for($i=1;$i<$length;$i++) {
        if ($first > $arr[$i]) {
            $left_array[] = $arr[$i];
        } else {
            $right_array[] = $arr[$i];
        }
    }
    $left_array = sort_two($left_array);
    $right_array = sort_two($right_array);
    return array_merge($left_array, [$first], $right_array);
}
$arr = [1,5,7,9,3,2];
function sort_three($arr) {
    $length = count($arr);
    for ($i = 0;$i < $length-1; $i++) {
        $p = $i;
        for ($j=$i+1;$j<$length;$j++) {
            if ($arr[$p] > $arr[$j]) {
                $p = $j;
            }
        }
        if ($p != $i) {
            $tmp = $arr[$i];
            $arr[$i] = $arr[$p];
            $arr[$p] = $tmp;
        }
    }
    return $arr;
}
// 方式二(从小到大排)
function insertSort($arr) {
    $len=count($arr);
    for ($i=1; $i<$len; $i++) {
        $tmp = $arr[$i];
        //内层循环控制，比较并插入
        for($j=$i-1;$j>=0;$j--) {
            if($tmp > $arr[$j]) {
                //发现插入的元素要大，交换位置，将后边的元素与前面的元素互换
                $arr[$j+1] = $arr[$j];
                $arr[$j] = $tmp;
            } else {
                //如果碰到不需要移动的元素，由于是已经排序好是数组，则前面的就不需要再次比较了。
                break;
            }
        }
        var_dump($arr);
    }
    return $arr;
}
var_dump(sort_four($arr));
function sort_four($arr) {
    $len = count($arr);
    for ($i=1;$i<$len;$i++) {
        $tmp = $arr[$i];
        for ($j=$i-1;$j>=0;$j--) {
            if ($tmp < $arr[$j]) {
                $arr[$j+1] = $arr[$j];
                $arr[$j] = $tmp;
            } else {
                break;
            }
        }
    }
    return $arr;
}
