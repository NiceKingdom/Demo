# 开发风格规范

## 通用

 - **单行长度**：最大 120 字符；
 - **方法注释**：每个类、方法，都需要携带注释；
 - **换行符**：统一为 Unix or OS x 格式（\n）；
 - **变量命名规则**：名词 + 修饰动词（eg. `$peopleOccupy`）；
 - **命名规则**：变量、函数、方法 - 小写开头驼峰式，类名 - 大写开头驼峰式，常量 - 全大写下划线分割；
     - **词汇搭配**：名词 + 动词，文件/类/变量采用`名词+动词`，方法则采用 `名词+动词`；
 - **内部注释**：每个逻辑块都应携带对应注释，若注释存在层级关系，推荐 `/* Description */` 作为大块注释，`// Description` 作为小块注释；
 - **状态码**：
     - 400：参数有误，服务器拒绝执行。
     - 403：服务器已经理解，但拒绝执行。
     - 500：服务器出现内部错误，无法完成任务。
 - **Commit Message**：言之有物，善用 `git stash`。
  - **PR**：如果您第一次提 PR，建议先去 issue 讨论一下您的想法，以免因为进入开发岔道。

## 后端

 - **方法参数**：必须强制参数类型，对混合类型必须进行校验；
 - **接口**：无论对内外, 统一返回数组, 且首位值必须是冗余状态 'succeed' / 'failed', 需要标示则另外建立变量；
 - **判断语句的容错**：善用日志。在有限可能性的判断语句中，用 `else` 触发日志系统(eg.01)。复合状态（多种条件共同判断）不受此限制；

## 规范示例

> 为部分较为抽象的规范提供示例，欢迎交流。

### 01

```php
// 共有三种单调状态，但此处仅有 a/b 两种单调状态：
if ($param === 'a') {
    // do something...
} elseif ($param === 'b') {
    // do something...
} else {
    // 触发日志
}
```

## 附录：命令

创建模型（及数据库迁移文件）：
`php artisan make:model Models/ModelName -m`

创建中间件：
`php artisan make:middleware Name`

执行数据库迁移并填充数据：
`php artisan migrate --seed`

执行重置数据库迁移并填充数据：
`php artisan migrate:refresh --seed`

执行移除数据库迁移：
`php artisan migrate:rollback`

运行一个 PHP 临时服务器：
`php artisan serve`（127.0.0.1:8000）

创建一个功能测试类：
`php artisan make:test UserTest`

创建一个单元测试类：
`php artisan make:test UserTest --unit`

运行 PHPUnit 测试（如果你使用 `phpunit --version` 获取到了不适应本项目测试文件的版本，那就来试试这个）：
`vendor/bin/phpunit --version`
