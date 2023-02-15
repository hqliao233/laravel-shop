<?php

namespace App\Admin\Controllers;

use App\Models\Product;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use function foo\func;

class ProductsController extends Controller
{
    use HasResourceActions;

    /**
     * 商品列表页
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('商品')
            ->body($this->grid());
    }

    /**
     * 编辑商品
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('编辑商品')
            ->body($this->form()->edit($id));
    }

    /**
     * 创建商品
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header('创建商品')
            ->body($this->form());
    }

    /**
     * 构建商品列表操作功能
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Product);

        $grid->id('ID')->sortable();
        $grid->title('商品名称');
        $grid->on_sale('是否已上架')->display(function ($value) {
            return $value ? '是' : '否';
        });
        $grid->rating('评分');
        $grid->price('价格');
        $grid->sold_count('销量');
        $grid->review_count('评论数');
        $grid->actions(function ($action) {
            $action->disableView();
            $action->disableDelete();
        });
        $grid->tools(function ($tool) {
            $tool->batch(function ($batch) {
                $batch->disableDelete();
            });
        });


        return $grid;
    }

    /**
     * 构建商品提交表单
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Product);

        $form->text('title', '商品名称')->rules('required');
        $form->editor('description', '商品描述')->rules('required');
        $form->image('image', '封面图片')->rules('required|image');
        $form->radio('on_sale', '上架')->options(['1' => '是', '0' => '否'])->default(0);

        // 直接添加一对多的关联模型
        $form->hasMany('skus', 'SKU 列表', function (Form\NestedForm $form) {
            $form->text('title', 'SKU 名称')->rules('required');
            $form->text('description', 'SKU 描述')->rules('required');
            $form->text('price', '单价')->rules('required|numeric|min:0.01');
            $form->text('stock', '剩余库存')->rules('required|integer|min:0');
        });

        // 定义事件回调，当模型即将保存时会触发这个回调
        $form->saving(function (Form $form) {
            $form->model()->price = collect($form->input('skus'))->where(Form::REMOVE_FLAG_NAME, 0)->min('price') ?: 0;
        });

        return $form;
    }
}
