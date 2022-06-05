<?php

namespace tsmd\address\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%address}}`.
 */
class M200601235948CreateAddressTable extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $table = '{{%address}}';
        $sql = <<<SQL
create table address (
    addrid    int(11) unsigned auto_increment primary key,
    uid       int(11) unsigned                       not null,
    consignee varchar(32)  default ''                not null comment '收件人',
    idnum     varchar(32)  default ''                not null comment '身份证字号',
    mobile    varchar(32)  default ''                not null comment '手机',
    phone     varchar(32)  default ''                not null comment '电话',
    email     varchar(192) default ''                not null comment '邮箱',
    country   varchar(4)   default ''                not null comment '国家代码 ISO-3166-1 alpha2',
    province  varchar(64)  default ''                not null comment '省',
    city      varchar(64)  default ''                not null comment '市',
    district  varchar(64)  default ''                not null comment '县区',
    street    varchar(64)  default ''                not null comment '街道',
    housenum  varchar(192) default ''                not null comment '门牌号',
    postcode  varchar(16)  default ''                not null comment '邮编',
    collType  varchar(16)  default ''                not null comment '代收点类型',
    collCode  varchar(16)  default ''                not null comment '代收点代号',
    brief     varchar(192) default ''                not null comment '摘要',
    tag       varchar(16)  default ''                not null comment '标签',
    pinyin    varchar(16)  default ''                not null comment '拼音',
    status    tinyint(2)   default 10                not null comment '状态',
    isPrimary tinyint(1)   default 0                 not null comment '默认',
    isLocked  tinyint(2)   default 0                 not null comment '锁定',
    extras    text                                   null,
    createdTime int(11) not null,
    updatedTime int(11) not null
) engine=innodb default charset=utf8mb4 collate=utf8mb4_unicode_ci;

create index uid on address (uid);
create index consignee on address (consignee);
create index idnum on address (idnum);
create index mobile on address (mobile);
create index createdTime on address (createdTime);

alter table {$table} auto_increment = 100001;
SQL;
        $this->getDb()->createCommand($sql)->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%address}}');
    }
}
