<?php

namespace App\Services;

use App\Models\UserAddress;

class UserAddressService
{
    /**
     * 获取当前操作用户所有收货地址
     * @param  object $user 当前操作用户
     * @return mixed
     */
    public function userAddresses($user)
    {
        return $user->addresses;
    }

    /**
     * 新增用户收货地址
     * @param object $user 当前操作用户
     * @param array  $data 需要保存的数据数组
     */
    public function storeUserAddress($user, $data)
    {
        $user->addresses()->create($data);
    }

    /**
     * 更新用户收货地址
     * @param UserAddress $address 需要更新的用户地址对象
     * @param array       $data    需要更新的内容
     */
    public function updateUserAddress(UserAddress $address, $data)
    {
        $address->update($data);
    }

    /**
     * 删除收货地址
     * @param  UserAddress $address 要删除的收货地址对象
     * @throws \Exception
     */
    public function deleteUserAddress(UserAddress $address)
    {
        $address->delete();
    }
}
