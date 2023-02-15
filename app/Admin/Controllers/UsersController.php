<?php

namespace App\Admin\Controllers;

use App\Models\User;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use function foo\func;

class UsersController extends Controller
{
    use HasResourceActions;

    /**
     * 用户列表页
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('用户列表')
            ->body($this->grid());
    }

    /**
     * 构建用户列表操作功能
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new User);

        $grid->id('ID');
        $grid->name('用户名');
        $grid->email('邮箱');
        $grid->email_verified_at('已验证邮箱')->display(function ($value) {
            return $value ? '是' : '否';
        });
        $grid->created_at('注册时间');
        //不显示新建用户按钮
        $grid->disableCreateButton();
        $grid->actions(function ($actions) {
            //不显示查看按钮
            $actions->disableView();
            //不显示删除按钮
            $actions->disableDelete();
            //不显示编辑按钮
            $actions->disableEdit();
        });

        $grid->tools(function ($tools) {
            //禁用批量删除按钮
            $tools->batch(function ($batch) {
                $batch->disableDelete();
            });
        });

        return $grid;
    }
}
