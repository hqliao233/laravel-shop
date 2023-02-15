<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserAddressRequest;
use App\Models\UserAddress;
use App\Services\UserAddressService;
use Illuminate\Http\Request;

class UserAddressesController extends Controller
{
    protected $userAddressService;

    /**
     * 注册UserAddressService
     * UserAddressesController constructor.
     * @param UserAddressService $userAddressService
     */
    public function __construct(UserAddressService $userAddressService)
    {
        $this->userAddressService = $userAddressService;
    }

    /**
     * 用户收货地址列表页
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $addresses = $this->userAddressService->userAddresses($request->user());

        return view('user_addresses.index', compact('addresses'));
    }

    /**
     * 跳转到新建收货地址页面
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $user_address = new UserAddress();

        return view('user_addresses.create_and_edit', compact('user_address'));
    }

    /**
     * 保存收货地址
     * @param UserAddressRequest $request        收货地址信息验证规则
     * @return \Illuminate\Http\RedirectResponse 跳转会用户收货地址首页
     */
    public function store(UserAddressRequest $request)
    {
        $this->userAddressService->storeUserAddress($request->user(), $request->all());

        return redirect()->route('user_addresses.index');
    }

    /**
     * 跳转到收货地址编辑页面
     * @param UserAddress $user_address 编辑的收货地址对象
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit(UserAddress $user_address)
    {
        $this->authorize('own', $user_address);

        return view('user_addresses.create_and_edit', compact('user_address'));
    }

    /**
     * 更新收货地址
     * @param UserAddressRequest $request 验证规则
     * @param UserAddress $user_address   更新收货地址对象
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UserAddressRequest $request, UserAddress $user_address)
    {
        $this->authorize('own', $user_address);

        $this->userAddressService->updateUserAddress($user_address, $request->all());

        return redirect()->route('user_addresses.index');
    }

    /**
     * 用户删除收货地址
     * @param UserAddress $user_address 被删除的地址
     * @return array                    ajax返回空数组
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Exception
     */
    public function destroy(UserAddress $user_address)
    {
        $this->authorize('own', $user_address);

        $this->userAddressService->deleteUserAddress($user_address);

        return [];
    }
}
