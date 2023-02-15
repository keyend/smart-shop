# 升级更新多门店为多商户 可自行添加商品
```
ALTER TABLE `xls_store`
ADD COLUMN `type` tinyint(1) NOT NULL DEFAULT 0 COMMENT '商户类型' AFTER `store_name`,
ADD COLUMN `is_custom` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否自行添加商品' AFTER `is_pickup`,
ADD COLUMN `is_recomment` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否启用推荐' AFTER `is_custom`,
ADD COLUMN `extra` mediumtext NULL COMMENT '商户类型' AFTER `uid`;
```

# 商户申请审核列表
```
INSERT INTO `xls_menu` (`id`, `app_module`, `addon`, `title`, `name`, `parent`, `level`, `url`, `is_show`, `sort`, `desc`, `is_icon`, `picture`, `picture_select`, `is_control`) VALUES ('577', 'shop', 'store', '入驻申请', 'STORE_APPVORD_LIST', 'STORE_ROOT', '2', 'store://admin/apply/lists', '1', '2', '', '0', 'app/shop/view/public/img/icon/store.png', '', '1');
INSERT INTO `xls_menu` (`id`, `app_module`, `addon`, `title`, `name`, `parent`, `level`, `url`, `is_show`, `sort`, `desc`, `is_icon`, `picture`, `picture_select`, `is_control`) VALUES ('578', 'shop', 'store', '审核通过', 'STORE_APPVORD_RESOLVE', 'STORE_LIST', '3', 'store://admin/apoly/resolve', '0', '1', '', '0', '', '', '1');
INSERT INTO `xls_menu` (`id`, `app_module`, `addon`, `title`, `name`, `parent`, `level`, `url`, `is_show`, `sort`, `desc`, `is_icon`, `picture`, `picture_select`, `is_control`) VALUES ('579', 'shop', 'store', '审核拒绝', 'STORE_APPVORD_REJECT', 'STORE_LIST', '3', 'store://admin/apply/reject', '0', '1', '', '0', '', '', '1');
INSERT INTO `xls_menu` (`id`, `app_module`, `addon`, `title`, `name`, `parent`, `level`, `url`, `is_show`, `sort`, `desc`, `is_icon`, `picture`, `picture_select`, `is_control`) VALUES ('580', 'shop', 'store', '商户结算', 'STORE_SETTLEMENT', 'STORE_ROOT', '2', 'store://admin/settlement/index', '1', '2', '', '0', '', '', '1');
INSERT INTO `xls_menu` (`id`, `app_module`, `addon`, `title`, `name`, `parent`, `level`, `url`, `is_show`, `sort`, `desc`, `is_icon`, `picture`, `picture_select`, `is_control`) VALUES ('581', 'shop', 'store', '商品列表', 'STORE_GOODS', 'STORE_ROOT', '2', 'store://admin/goods/lists', '1', '2', '', '0', '', '', '1');
INSERT INTO `xls_menu` (`id`, `app_module`, `addon`, `title`, `name`, `parent`, `level`, `url`, `is_show`, `sort`, `desc`, `is_icon`, `picture`, `picture_select`, `is_control`) VALUES ('582', 'shop', 'store', '订单列表', 'STORE_ORDER', 'STORE_ROOT', '2', 'store://admin/order/lists', '1', '2', '', '0', '', '', '1');
INSERT INTO `xls_menu` (`id`, `app_module`, `addon`, `title`, `name`, `parent`, `level`, `url`, `is_show`, `sort`, `desc`, `is_icon`, `picture`, `picture_select`, `is_control`) VALUES ('583', 'shop', 'store', '订单明细', 'STORE_ORDER_DETAIL', 'STORE_ORDER', '3', 'store://admin/order/detail', '0', '1', '', '0', '', '', '1');
INSERT INTO `xls_menu` (`id`, `app_module`, `addon`, `title`, `name`, `parent`, `level`, `url`, `is_show`, `sort`, `desc`, `is_icon`, `picture`, `picture_select`, `is_control`) VALUES ('584', 'shop', 'store', '商户设置', 'STORE_CONFIG', 'STORE_ROOT', '2', 'store://admin/config/index', '1', '2', '', '0', '', '', '1');
```

# 商品类型

# | 商品所属 | 发货人
-- | ---- | --------
1. | 平台商品 | 平台发货
2. | 平台商品 | 到店提货
3. | 平台商品 | 商户发货
4. | 商户商品 | 商户发货