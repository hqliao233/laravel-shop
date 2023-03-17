<?php

/**
 * 此方法会将当前请求的路由名称转换为 CSS 类名称，作用是允许我们针对某个页面做页面样式定制
 * @return mixed
 */
function route_class()
{
    return str_replace('.', '-', Route::currentRouteName());
}

/**
 * 数组多维聚合方法，根据给定的 $keys 聚合数组，需要保证数组每个元素都包含keys里面所有的键
 *
 * @param array $array 需要进行聚合的数组
 * @param array|string $keys 被聚合数组每个元素都应该存在的键组成的数组或字符串
 * @return array
 */
function array_group_by($array, $keys)
{
    // $keys 参数判断，非字符串或数组直接返回 $array
    if (!is_string($keys) && !is_array($keys)) {
        return $array;
    }

    // $keys 为字符串或者只有一个元素的数组可以直接进行聚合操作
    if (is_string($keys) || (is_array($keys) && 1 == count($keys))) {

        // 获取到进行聚合的 $key
        $key = is_array($keys) ? array_shift($keys) : $keys;

        // 使用回调函数将键 $key 的值相同的元素放到同一个 $key 的数组下面
        return array_reduce($array, function ($grouped_array, $item) use ($key) {
            $grouped_array[$item[$key]][] = $item;

            return $grouped_array;
        });
    } else {
        // $keys 存在多个的情况，先用第一个 $keys 元素将数组进行聚合
        $grouped_array = array_group_by($array, array_shift($keys));

        // 到了这里 $keys 的长度就是 n-1
        // 循环聚合后的数组再对聚合后的数组的每个元素继续进行 $keys(n-1) 的聚合，一直回调处理就能够达到多重聚合的效果
        foreach ($grouped_array as $index => $current_group_array) {
            $grouped_array[$index] = array_group_by($current_group_array, $keys);
        }

        return $grouped_array;
    }
}

/**
 * 数组无限极分类
 *
 * @param array $array 需要进行无限极分类的数组
 * @param array $parent_value_array 是初始数组中所有父级 $parent_key 值数组
 * @param string $key 数组元素的唯一识别键名
 * @param string $parent_key 数组元素的父级识别键名
 * @param int $parent_value 当前查询的父级识别键名的值
 * @param string $children_key 存在子级情况下的子级分类数组存放的键名
 * @return array
 */
function array_tree_by($array, $parent_value_array, $key = 'id', $parent_key = 'parent_id', $parent_value = 0, $children_key = 'children')
{
    // 键 $parent_key 的值为当前 $parent_value 的子级数据
    $tree_array = [];

    foreach ($array as $index => $item) {

        // 如果当前分级父级ID和当前元素父级ID值相同把该元素放到其父级元素数组里面
        if ($parent_value == $item[$parent_key]) {

            // 如果当前元素也是一个父级元素的话进行递归处理
            if (in_array($item[$key], $parent_value_array)) {
                $array[$index][$children_key] = array_tree_by($array, $parent_value_array, $key, $parent_key, $item[$key], $children_key);
            }

            $tree_array[] = &$array[$index];
        }
    }

    return $tree_array;
}
