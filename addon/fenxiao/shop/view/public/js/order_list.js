/**
 * 渲染订单列表
 */
Order = function () {
};

/**
 * 设置数据集
 */
Order.prototype.setData = function (data) {
	Order.prototype.data = data;
};

/**
 * 列名数据
 */
Order.prototype.cols = [
	{
		title: '<span>商品</span>',
		width: "30%",
		className: "product-info",
		template: function (orderitem, order) {
			
			var h = '<div class="img-block">';
			h += '<img layer-src src="' + ns.img(orderitem.sku_image,'small') + '">';
			h += '</div>';
			h += '<div class="info">';
			h += '<a href="' + ns.url("shop/order/detail", {order_id: orderitem.order_id}) + '" target="_blank" title="' + orderitem.sku_name + '" class="ns-multi-line-hiding ns-text-color">' + orderitem.sku_name + '</a>';
			return h;
		}
	},
	{
		title: "单价(元) / 数量",
		width: "10%",
		align: "right",
		className: "order-price",
		template: function (orderitem, order) {
			var h = '<div style="padding-right: 15px;">';
			h += '<div>';
			h += '<span>' + orderitem.price + '</span>';
			h += '</div>';
			h += '<div>';
			h += '<span>' + orderitem.num + '件</span>';
			h += '</div>';
			h += '</div>';
			return h;
		}
	},
	{
		title: "买家信息",
		width: "15%",
		align: "left",
		className: "buyers",
		merge: true,
		template: function (orderitem, order) {
			var h = '';
			if (order.order_type != 4) {
				h += '<p>';
				h += '<a href="javascript:;">' + order.name + '</a>';
				h += '</p>';
				h += '<span>' + order.mobile + '</span>';
				h += '<span class="ns-line-hiding" title="' + order.full_address + '">' + order.full_address + '</span>';
			} else {
				h = '<p>';
				h += '<span>' + order.mobile + '</span>';
				h += '</p>';
			}
			
			return h;
		}
	},
	{
		title: "订单状态",
		width: "10%",
		align: "center",
		className: "transaction-status",
		merge: true,
		template: function (orderitem, order) {
			var html = '<div>' + order.order_status_name + '</div>';
			return html;
		}
	},
	{
	    title : "分佣信息",
	    width : "20%",
	    align : "left",
	    className : "create-time",
	    merge : false,
	    template : function(orderitem,order){
	    	var html = '<div>一级分佣：'+ (orderitem.one_commission > 0 ? orderitem.one_fenxiao_name +' ¥' + orderitem.one_commission : '--') +'</div>';
	    		html += '<div>二级分佣：'+ (orderitem.two_commission > 0 ? orderitem.two_fenxiao_name +' ¥' + orderitem.two_commission : '--') +'</div>';
	    		html += '<div>三级分佣：'+ (orderitem.three_commission > 0 ? orderitem.three_fenxiao_name +' ¥' + orderitem.three_commission : '--') +'</div>';
    		return html;
	    }
	},
	{
	    title : "佣金状态",
	    width : "15%",
	    align : "center",
	    className : "settlement",
	    merge : true,
	    template : function(orderitem,order){
	        var settlement_name = order.is_settlement == 1 ? "已结算" : "待结算";
	        return '<div>'+settlement_name+'</div>';
	    }
	},
];

/**
 * 渲染表头
 */
Order.prototype.header = function (hasThead) {
	var colgroup = '<colgroup>';
	var thead = '';
	if (hasThead) thead = '<thead><tr>';
	
	for (var i = 0; i < this.cols.length; i++) {
		var align = this.cols[i].align ? "text-align:" + this.cols[i].align : "";
		
		colgroup += '<col width="' + this.cols[i].width + '">';
		if (hasThead) {
			thead += '<th style="' + align + '" class="' + (this.cols[i].className || "") + '">';
			thead += '<div class="layui-table-cell">' + this.cols[i].title + '</div>';
			thead += '</th>';
		}
	}
	colgroup += '</colgroup>';
	if (hasThead) thead += '</tr></thead>';
	return colgroup + thead;
};

/**
 * 渲染内容
 */
Order.prototype.tbody = function () {

	var tbody = '<tbody>';
	for (var i = 0; i < this.data.list.length; i++) {

		var order = this.data.list[i];
		var orderitemList = order.order_goods;
		var pay_type_name = order.pay_type_name != '' ? order.pay_type_name : "";

		if (i > 0) {
			//分割行
			tbody += '<tr class="separation-row">';
			tbody += '<td colspan="' + this.cols.length + '"></td>';
			tbody += '</tr>';
		}

		//订单项头部
		tbody += '<tr class="header-row">';
		tbody += '<td colspan="5">';
		tbody += '<span class="order-item-header" style="margin-right:10px;">订单号：' + order.order_no + '</span>';

		tbody += '<span class="order-item-header" style="margin-left:50px;">下单时间：' + ns.time_to_date(order.create_time) + '</span>';

		tbody += '</td>';
		tbody += '<td>';
			tbody += '<div class="ns-table-btn" style="justify-content: flex-end;">';
			tbody += '<a class="layui-btn layui-btn-xs" href="' + ns.url("fenxiao://shop/order/detail", {order_id: order.order_id}) + '" target="_blank">查看详情</a>';
			tbody += '</div>';
		tbody += '</td>';
		tbody += '</tr>';

		var orderitemHtml = '';
		loadImgMagnify();
		for (var j = 0; j < orderitemList.length; j++) {
			var orderitem = orderitemList[j];
			orderitemHtml += '<tr class="content-row">';
			for (var k = 0; k < this.cols.length; k++) {

				if (j == 0 && this.cols[k].merge && this.cols[k].template) {

					orderitemHtml += '<td class="' + (this.cols[k].className || "") + '" align="' + (this.cols[k].align || "") + '" style="' + (this.cols[k].style || "") + '" rowspan="' + orderitemList.length + '">';
					orderitemHtml += this.cols[k].template(orderitem, order);
					orderitemHtml += '</td>';

				} else if (this.cols[k].template && !this.cols[k].merge) {

					orderitemHtml += '<td class="' + (this.cols[k].className || "") + '" align="' + (this.cols[k].align || "") + '" style="' + (this.cols[k].style || "") + '">';
					orderitemHtml += this.cols[k].template(orderitem, order);
					orderitemHtml += '</td>';

				}
			}
			orderitemHtml += '</tr>';
		}

		tbody += orderitemHtml;
	}

	tbody += '</tbody>';
	return tbody;
};

/**
 * 渲染表格
 */
Order.prototype.fetch = function () {
	if (this.data.list.length > 0) {
		return '<table class="layui-table layui-form">' + this.header(true) + '</table><table class="layui-table order-list-table layui-form">' + this.header(false) + this.tbody() + '</table>';
	} else {
		return '<table class="layui-table order-list-table layui-form">' + this.header(true) + '</table>' + '<div class="order-no-data-block"><ul><li><i class="layui-icon layui-icon-tabs"></i> </li><li>暂无订单</li></ul></div>';
	}
};

function showMore(order_id) {
	$(".more-operation[data-order-id]").hide();
	$(".more-operation[data-order-id='" + order_id + "']").show();
	$("body").click(function (e) {
		if (!$(e.target).closest(".order-item-header.more").length) {
			$(".more-operation[data-order-id='" + order_id + "']").hide();
		}
	});
}