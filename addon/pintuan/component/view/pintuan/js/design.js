// 顶部内容组件
var pintuanTopConHtml = '<div class="goods-head">';
	pintuanTopConHtml +=	'<div class="title-wrap">';
	pintuanTopConHtml +=		'<div class="left-icon" v-if="list.imageUrl"><img v-bind:src="$parent.$parent.changeImgUrl(list.imageUrl)" /></div>';
	pintuanTopConHtml +=		'<span class="name" v-bind:style="{color: data.titleTextColor?data.titleTextColor:\'rgba(0,0,0,0)\'}">{{list.title}}</span>';
	pintuanTopConHtml +=	'</div>';
	pintuanTopConHtml +=	'<div class="more ns-red-color" v-if="listMore.title">';
	pintuanTopConHtml +=		'<span v-bind:style="{color: data.moreTextColor?data.moreTextColor:\'rgba(0,0,0,0)\'}">{{listMore.title}}</span>';
	pintuanTopConHtml +=		'<div class="right-icon" v-if="listMore.imageUrl"><img v-bind:src="$parent.$parent.changeImgUrl(listMore.imageUrl)" /></div>';
	pintuanTopConHtml +=		'<i class="iconfont iconyoujiantou" v-else v-bind:style="{color: data.moreTextColor?data.moreTextColor:\'rgba(0,0,0,0)\'}"></i>';
	pintuanTopConHtml +=	'</div>';
	pintuanTopConHtml +='</div>';

Vue.component("pintuan-top-content", {
	data: function () {
		return {
			data: this.$parent.data,
			list: this.$parent.data.list,
			listMore: this.$parent.data.listMore
		}
	},
	created: function () {
		if(!this.$parent.data.verify) this.$parent.data.verify = [];
		this.$parent.data.verify.push(this.verify);//加载验证方法
	},
	methods: {
		verify : function () {
			var res = { code : true, message : "" };
			return res;
		},
	},
	template: pintuanTopConHtml
});

/**
 * 空的验证组件，后续如果增加业务，则更改组件
 */
var pintuanListHtml = '<div class="goods-list-edit layui-form">';

		pintuanListHtml += '<div class="layui-form-item">';
			pintuanListHtml += '<label class="layui-form-label sm">商品来源</label>';
			pintuanListHtml += '<div class="layui-input-block">';
				pintuanListHtml += '<template v-for="(item,index) in goodsSources" v-bind:k="index">';
					pintuanListHtml += '<div v-on:click="data.sources=item.value" v-bind:class="{ \'layui-unselect layui-form-radio\' : true,\'layui-form-radioed\' : (data.sources==item.value) }"><i class="layui-anim layui-icon">&#xe643;</i><div>{{item.text}}</div></div>';
				pintuanListHtml += '</template>';
			pintuanListHtml += '</div>';
		pintuanListHtml += '</div>';
		
		pintuanListHtml += '<div class="layui-form-item" v-if="data.sources == \'diy\'">';
			pintuanListHtml += '<label class="layui-form-label sm">手动选择</label>';
			pintuanListHtml += '<div class="layui-input-block">';
				pintuanListHtml += '<a href="#" class="ns-input-text ns-text-color" v-on:click="addGoods">选择</a>';
			pintuanListHtml += '</div>';
		pintuanListHtml += '</div>';
		
		pintuanListHtml += '<div class="layui-form-item" v-show="data.sources == \'default\'">';
			pintuanListHtml += '<label class="layui-form-label sm">商品数量</label>';
			pintuanListHtml += '<div class="layui-input-block">';
				// pintuanListHtml += '<input class="layui-input goods-account" v-model="data.goodsCount" />';
				pintuanListHtml += '<input type="number" class="layui-input goods-account" v-on:keyup="shopNum" v-model="data.goodsCount"/>';
			pintuanListHtml += '</div>';
		pintuanListHtml += '</div>';
		
		pintuanListHtml += '<div class="layui-form-item" v-show="data.sources == \'default\'">';
			pintuanListHtml += '<label class="layui-form-label sm"></label>';
			pintuanListHtml += '<div class="layui-input-block">';
				pintuanListHtml += '<template v-for="(item,index) in goodsCount" v-bind:k="index">';
					pintuanListHtml += '<div v-on:click="data.goodsCount=item" v-bind:class="{ \'layui-unselect layui-form-radio\' : true,\'layui-form-radioed\' : (data.goodsCount==item) }"><i class="layui-anim layui-icon">&#xe643;</i><div>{{item}}</div></div>';
				pintuanListHtml += '</template>';
			pintuanListHtml += '</div>';
		pintuanListHtml += '</div>';

		// pintuanListHtml += '<p class="hint">商品数量选择 0 时，前台会自动上拉加载更多</p>';
		
	pintuanListHtml += '</div>';

var select_goods_list = []; //配合商品选择器使用
Vue.component("pintuan-list", {
	template: pintuanListHtml,
	data: function () {
		return {
			data: this.$parent.data,
			goodsSources: [
				{
					text: "默认",
					value: "default"
				},
				{
					text : "手动选择",
					value : "diy"
				}
			],
			categoryList: [],
			isLoad: false,
			isShow: false,
			selectIndex: 0,//当前选中的下标
			goodsCount: [6, 12, 18, 24, 30],
		}
	},
	created:function() {
		if(!this.$parent.data.verify) this.$parent.data.verify = [];
		this.$parent.data.verify.push(this.verify);//加载验证方法
	},
	methods: {
		shopNum: function(){
			if (this.$parent.data.goodsCount > 50){
				layer.msg("商品数量最多为50");
				this.$parent.data.goodsCount = 50;
			}
			if (this.$parent.data.goodsCount.length > 0 && this.$parent.data.goodsCount < 1) {
				layer.msg("商品数量不能小于0");
				this.$parent.data.goodsCount = 1;
			}
		},
		verify : function () {
			var res = { code : true, message : "" };
			if(this.$parent.data.goodsCount.length===0) {
				res.code = false;
				res.message = "请输入商品数量";
			}
			if (this.$parent.data.goodsCount < 0) {
				res.code = false;
				res.message = "商品数量不能小于0";
			}
			if(this.$parent.data.goodsCount > 50){
				res.message = "商品数量最多为50";
			}
			return res;
		},
		addGoods: function(){
			var self = this;

			goodsSelect(function (res) {

				// if (!res.length) return false;
				self.$parent.data.goodsId = [];
				for (var i=0; i<res.length; i++) {
					self.$parent.data.goodsId.push(res[i]);
				}

			}, self.$parent.data.goodsId, {mode: "spu", promotion: "pintuan",disabled:0});
		}
	}
});

var pintuanStyleHtml = '<div class="layui-form-item">';
		pintuanStyleHtml += '<label class="layui-form-label sm">选择风格</label>';
		pintuanStyleHtml += '<div class="layui-input-block">';
			pintuanStyleHtml += '<div class="ns-input-text ns-text-color selected-style" v-on:click="selectGroupbuyStyle">选择</div>';
		pintuanStyleHtml += '</div>';
	pintuanStyleHtml += '</div>';

Vue.component("pintuan-style", {
	template: pintuanStyleHtml,
	data: function() {
		return {
			data: this.$parent.data,
		}
	},
	created:function() {
		if(!this.$parent.data.verify) this.$parent.data.verify = [];
		this.$parent.data.verify.push(this.verify);//加载验证方法
	},
	methods: {
		verify: function () {
			var res = { code: true, message: "" };
			return res;
		},
		selectGroupbuyStyle: function() {
			var self = this;
			layer.open({
				type: 1,
				title: '风格选择',
				area:['930px','630px'],
				btn: ['确定', '返回'],
				content: $(".draggable-element[data-index='" + self.data.index + "'] .edit-attribute .pintuan-list-style").html(),
				success: function(layero, index) {
					$(".layui-layer-content input[name='style']").val(self.data.style);
					$("body").on("click", ".layui-layer-content .style-list-con-pintuan .style-li-pintuan", function () {
						$(this).addClass("selected ns-border-color").siblings().removeClass("selected ns-border-color");
						$(".layui-layer-content input[name='style']").val($(this).index() + 1);
					});
				},
				yes: function (index, layero) {
					self.data.style = $(".layui-layer-content input[name='style']").val();
					layer.closeAll()
				}
			});
		},
	}
})

// 图片上传
var pintuanTopHtml = '<ul class="fenxiao-addon-title">';
		pintuanTopHtml += '<li>';
		
			pintuanTopHtml += '<div class="layui-form-item">';
				pintuanTopHtml += '<label class="layui-form-label sm">左侧图标</label>';
				pintuanTopHtml += '<div class="layui-input-block">';
					pintuanTopHtml += '<img-upload v-bind:data="{ data : list }"></img-upload>';
				pintuanTopHtml += '</div>';
				pintuanTopHtml += '<div class="ns-word-aux ns-diy-word-aux">建议上传图标大小：20px * 20px</div>';
			pintuanTopHtml += '</div>';
			
			// pintuanTopHtml += '<img-upload v-bind:data="{ data : list }"></img-upload>';
			pintuanTopHtml += '<div class="content-block">';
				pintuanTopHtml += '<div class="layui-form-item">';
					pintuanTopHtml += '<label class="layui-form-label sm">标题</label>';
					pintuanTopHtml += '<div class="layui-input-block">';
						pintuanTopHtml += '<input type="text" name=\'title\' v-model="list.title" class="layui-input" />';
					pintuanTopHtml += '</div>';
				pintuanTopHtml += '</div>';
			pintuanTopHtml += '</div>';
			
			pintuanTopHtml += '<color v-bind:data="{ field : \'titleTextColor\', label : \'标题颜色\', defaultcolor: \'#000\' }"></color>';
		pintuanTopHtml += '</li>';
		
		pintuanTopHtml += '<li>';
			// pintuanTopHtml += '<div class="layui-form-item">';
			// 	pintuanTopHtml += '<label class="layui-form-label sm">右侧图标</label>';
			// 	pintuanTopHtml += '<div class="layui-input-block">';
			// 		pintuanTopHtml += '<img-upload v-bind:data="{ data : item }"></img-upload>';
			// 	pintuanTopHtml += '</div>';
			// pintuanTopHtml += '</div>';
			
			pintuanTopHtml += '<div class="content-block">';
				pintuanTopHtml += '<div class="layui-form-item">';
					pintuanTopHtml += '<label class="layui-form-label sm">文本内容</label>';
					pintuanTopHtml += '<div class="layui-input-block">';
						pintuanTopHtml += '<input type="text" name=\'title\' v-model="listMore.title" class="layui-input" />';
					pintuanTopHtml += '</div>';
				pintuanTopHtml += '</div>';
				pintuanTopHtml += '<color v-bind:data="{ field : \'moreTextColor\', defaultcolor: \'#858585\' }"></color>';
				
				// pintuanTopHtml += '<nc-link v-bind:data="{ field : $parent.data.list[index].link }"></nc-link>';
				
			pintuanTopHtml += '</div>';
		pintuanTopHtml += '</li>';
	pintuanTopHtml += '</ul>';

Vue.component("pintuan-top-list",{
	template : pintuanTopHtml,
	data : function(){
		return {
            data : this.$parent.data,
			list : this.$parent.data.list,
			listMore: this.$parent.data.listMore
		};
	},
	created : function(){
		if(!this.$parent.data.verify) this.$parent.data.verify = [];
		this.$parent.data.verify.push(this.verify);//加载验证方法
	},
	watch : {

	},
	methods : {
		verify:function () {
			var res = { code : true, message : "" };
			var _self = this;
			$(".draggable-element[data-index='" + this.data.index + "'] .graphic-navigation .graphic-nav-list>ul>li").each(function(index){
				
				if(_self.selectedTemplate == "imageNavigation"){
					$(this).find("input[name='title']").removeAttr("style");//清空输入框的样式
					//检测是否有未上传的图片
					if(_self.list[index].imageUrl == ""){
						res.code = false;
						res.message = "请选择一张图片";
						$(this).find(".error-msg").text("请选择一张图片").show();
						return res;
					}else{
						$(this).find(".error-msg").text("").hide();
					}
				}else{
					if(_self.list[index].title == ""){
						res.code = false;
						res.message = "请输入标题";
						$(this).find("input[name='title']").attr("style","border-color:red !important;").focus();
						$(this).find(".error-msg").text("请输入标题").show();
						return res;
					}else{
						$(this).find("input[name='title']").removeAttr("style");
						$(this).find(".error-msg").text("").hide();
					}
				}
			});
			return res;
		}
	}
});

