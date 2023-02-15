<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCouponCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupon_codes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->comment('优惠卷标题');
            $table->string('code')->unique()->comment('优惠码');
            $table->string('type')->comment('优惠卷类型,支持固定金额和百分比');
            $table->decimal('value')->comment('折扣值，根据不同类型含义不同');
            $table->unsignedInteger('total')->comment('全站可兑换的数量');
            $table->unsignedInteger('used')->default(0)->comment('已兑换的数量');
            $table->decimal('min_amount', 10, 2)->comment('使用该优惠劵最低订单金额');
            $table->dateTime('not_before')->nullable()->comment('在这个时间之前不可用');
            $table->dateTime('not_after')->nullable()->comment('在这个时间之后不可用');
            $table->boolean('enabled')->comment('是否有效');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coupon_codes');
    }
}
