var notesHtml = '<div>';
		notesHtml += '<div class="layui-form-item">';
			notesHtml += '<label class="layui-form-label sm">数据来源</label>';
			notesHtml += '<div class="layui-input-block">';
				notesHtml += '<template v-for="(item,index) in goodsSources" v-bind:k="index">';
					notesHtml += '<div v-on:click="data.sources=item.value" v-bind:class="{ \'layui-unselect layui-form-radio\' : true,\'layui-form-radioed\' : (data.sources==item.value) }"><i class="layui-anim layui-icon">&#xe643;</i><div>{{item.text}}</div></div>';
				notesHtml += '</template>';
			notesHtml += '</div>';
		notesHtml += '</div>';
		
		notesHtml += '<div class="layui-form-item" v-if="data.sources != \'diy\'">';
			notesHtml += '<label class="layui-form-label sm">显示数量</label>';
			notesHtml += '<div class="layui-input-block">';
				// bargainListHtml += '<input class="layui-input goods-account" v-model="data.goodsCount" />';
				notesHtml += '<input type="number" class="layui-input goods-account ns-len-short" v-on:keyup="shopNum" v-model="data.goodsCount"/>';
			notesHtml += '</div>';
		notesHtml += '</div>';
		
		notesHtml += '<div class="layui-form-item" v-if="data.sources == \'diy\'">';
			notesHtml += '<label class="layui-form-label sm">手动选择</label>';
			notesHtml += '<div class="layui-input-block">';
				notesHtml += '<a href="#" class="ns-input-text ns-text-color" v-on:click="addNotes">选择</a>';
			notesHtml += '</div>';
		notesHtml += '</div>';
		
		notesHtml += '<div class="layui-form-item">';
			notesHtml += '<label class="layui-form-label sm">选择风格</label>';
			notesHtml += '<div class="layui-input-block choose-style">';
				notesHtml += '<div class="ns-input-text ns-text-color selected-style" v-on:click="selectTestStyle">选择</div>';
			notesHtml += '</div>';
		notesHtml += '</div>';
	notesHtml += '</div>';
Vue.component("notes-style", {
	template: notesHtml,
	data: function() {
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
			goodsCount: [3, 6, 9],
			selectIndex: 0
		}
	},
	created: function () {
		if(!this.$parent.data.verify) this.$parent.data.verify = [];
		this.$parent.data.verify.push(this.verify);//加载验证方法
	},
	methods: {
		verify : function () {
			var res = { code : true, message : "" };
			if(this.$parent.data.goodsCount.length===0) {
				res.code = false;
				res.message = "请输入首页展示笔记数量";
			}
			if (this.$parent.data.goodsCount < 0) {
				res.code = false;
				res.message = "首页展示笔记数量不能小于0";
			}
			if(this.$parent.data.goodsCount > 50){
				res.message = "首页展示笔记数量最多为50";
			}
			return res;
		},
		shopNum: function(){
			if (this.$parent.data.goodsCount > 50){
				layer.msg("首页展示数量最多为50");
				this.$parent.data.goodsCount = 50;
			}
			if (this.$parent.data.goodsCount.length > 0 && this.$parent.data.goodsCount < 1) {
				layer.msg("首页展示数量不能小于0");
				this.$parent.data.goodsCount = 1;
			}
		},
		addNotes: function(){
			var self = this;
			notesSelect(function (res) {
				// if (!res.length) return false;
				self.$parent.data.goodsId = [];
				for (var i=0; i<res.length; i++) {
					self.$parent.data.goodsId.push(res[i]);
				}
			}, self.$parent.data.goodsId, {});
		},
		selectTestStyle: function() {
			var self = this;
			layer.open({
				type: 1,
				title: '风格选择',
				area:['930px','630px'],
				btn: ['确定', '返回'],
				content: $(".draggable-element[data-index='" + self.data.index + "'] .edit-attribute .notes-list-style").html(),
				success: function(layero, index) {
					$(".layui-layer-content input[name='style']").val(self.data.style);
					$("body").on("click", ".layui-layer-content .style-list-con-notes .style-li-notes", function () {
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

// 多选
var notesContentHtml = '<div class="layui-form-item goods-show-box">';
	notesContentHtml +=	'<div class="layui-input-inline">';
	notesContentHtml +=		'<div v-on:click="changeStatus(\'notesLabel\')" class="layui-unselect layui-form-checkbox" v-bind:class="{\'layui-form-checked\': (data.notesLabel == 1)}" lay-skin="primary"><span>亮点</span><i class="layui-icon layui-icon-ok"></i></div>';
	notesContentHtml +=		'<div v-on:click="changeStatus(\'readNum\')" class="layui-unselect layui-form-checkbox" v-bind:class="{\'layui-form-checked\': (data.readNum == 1)}" lay-skin="primary"><span>阅读数</span><i class="layui-icon layui-icon-ok"></i></div>';
	// notesContentHtml +=		'<div v-on:click="changeStatus(\'thumbsUpNum\')" class="layui-unselect layui-form-checkbox" v-bind:class="{\'layui-form-checked\': (data.thumbsUpNum == 1)}" lay-skin="primary"><span>点赞数</span><i class="layui-icon layui-icon-ok"></i></div>';
	notesContentHtml +=		'<div v-on:click="changeStatus(\'uploadTime\')" class="layui-unselect layui-form-checkbox" v-bind:class="{\'layui-form-checked\': (data.uploadTime == 1)}" lay-skin="primary"><span>更新时间</span><i class="layui-icon layui-icon-ok"></i></div>';
	notesContentHtml +=	'</div>';
	notesContentHtml += '</div>';

Vue.component("notes-content", {
	template: notesContentHtml,
	data: function () {
		return {
			data: this.$parent.data,
		};
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
		changeStatus: function(field) {
			this.$parent.data[field] = this.$parent.data[field] ? 0 : 1;
		}
	}
});


// 顶部内容组件
var notesTopConHtml = '<div class="goods-head">';
	notesTopConHtml +=	'<div class="title-wrap">';
	notesTopConHtml +=		'<span class="name" v-bind:style="{color: data.titleTextColor?data.titleTextColor:\'rgba(0,0,0,0)\'}">{{list.title}}</span>';
	notesTopConHtml +=	'</div>';
	notesTopConHtml +=	'<div class="more ns-red-color" v-if="listMore.title">';
	notesTopConHtml +=		'<span v-bind:style="{color: data.moreTextColor?data.moreTextColor:\'rgba(0,0,0,0)\'}">{{listMore.title}}</span>';
	notesTopConHtml +=		'<i class="iconfont iconyoujiantou" v-bind:style="{color: data.moreTextColor?data.moreTextColor:\'rgba(0,0,0,0)\'}"></i>';
	notesTopConHtml +=	'</div>';
	notesTopConHtml +='</div>';

Vue.component("notes-top-content", {
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
	template: notesTopConHtml
});


// 图片上传
var notesTopHtml = '<ul class="fenxiao-addon-title">';
		notesTopHtml += '<li>';
			notesTopHtml += '<div class="content-block">';
				notesTopHtml += '<div class="layui-form-item">';
					notesTopHtml += '<label class="layui-form-label sm">标题</label>';
					notesTopHtml += '<div class="layui-input-block">';
						notesTopHtml += '<input type="text" name=\'title\' v-model="list.title" class="layui-input" />';
					notesTopHtml += '</div>';
				notesTopHtml += '</div>';
			notesTopHtml += '</div>';
			
			notesTopHtml += '<color v-bind:data="{ field : \'titleTextColor\', label : \'标题颜色\', defaultcolor: \'#333333\' }"></color>';
		notesTopHtml += '</li>';
		
		notesTopHtml += '<li>';
			notesTopHtml += '<div class="content-block">';
				notesTopHtml += '<div class="layui-form-item">';
					notesTopHtml += '<label class="layui-form-label sm">文本内容</label>';
					notesTopHtml += '<div class="layui-input-block">';
						notesTopHtml += '<input type="text" name=\'title\' v-model="listMore.title" class="layui-input" />';
					notesTopHtml += '</div>';
				notesTopHtml += '</div>';
				notesTopHtml += '<color v-bind:data="{ field : \'moreTextColor\', defaultcolor: \'#858585\' }"></color>';
			notesTopHtml += '</div>';
		notesTopHtml += '</li>';
	notesTopHtml += '</ul>';

Vue.component("notes-top-list",{
	template : notesTopHtml,
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

/* var notesMoreHtml = '<div class="layui-form-item">';
	notesMoreHtml +=	 '<label class="layui-form-label sm">{{data.label}}</label>';
	notesMoreHtml +=	 '<div class="layui-input-block">';
	notesMoreHtml +=		 '<template v-for="(item,index) in list" v-bind:k="index">';
	notesMoreHtml +=			 '<div v-on:click="parent[data.field]=item.value" v-bind:class="{ \'layui-unselect layui-form-radio\' : true,\'layui-form-radioed\' : (parent[data.field]==item.value) }"><i class="layui-anim layui-icon">&#xe643;</i><div>{{item.label}}</div></div>';
	notesMoreHtml +=		 '</template>';
	notesMoreHtml +=	 '</div>';
	notesMoreHtml += '</div>';

Vue.component("notes-more", {
	props: {
		data: {
			type: Object,
			default: function () {
				return {
					field: "notesMore",
					label: "查看更多"
				};
			}
		}
	},
	data: function () {
		return {
			list: [
				{label: "开启", value: 1},
				{label: "关闭", value: 2},
			],
			parent: this.$parent.data,
		};
	},
	created: function () {
		if(!this.$parent.data.verify) this.$parent.data.verify = [];
		this.$parent.data.verify.push(this.verify);//加载验证方法
		if (this.data.label == undefined) this.data.label = "查看更多";
		if (this.data.field == undefined) this.data.field = "notesMore";
		
		var self = this;
		setTimeout(function () {
			layui.use(['form'], function() {
				self.form = layui.form;
				self.form.render();
			});
		},10);
	},
	watch: {
		data: function (val, oldVal) {
			if (val.field == undefined) val.field = oldVal.field;
			if (val.label == undefined) val.label = "查看更多";
		},
	},
	methods: {
		verify : function () {
			var res = { code : true, message : "" };
			return res;
		},
	},
	template: notesMoreHtml
}); */