# JingdongCloudTradeBundle 测试套件

这个目录包含了 JingdongCloudTradeBundle 的单元测试套件，使用 PHPUnit 框架实现。

## 测试结构

测试用例按照被测试组件的类型和功能进行了分类：

- `Entity/` - 实体类测试
- `Service/` - 服务类测试
- `Enum/` - 枚举类测试
- `Repository/` - 仓库类测试（如需添加）
- `Controller/` - 控制器测试（如需添加）
- `EventSubscriber/` - 事件订阅器测试（如需添加）

## 测试范围

测试覆盖了以下组件和功能：

1. 实体类
   - 基本属性和关系
   - getter 和 setter 方法
   - 辅助方法（如过期检查、格式化等）

2. 服务类
   - 与京东 API 的交互
   - 数据转换和格式化
   - 授权和认证功能

3. 枚举类
   - 枚举值的正确性
   - 辅助方法（如获取标签、选项等）

## 运行测试

执行以下命令运行所有测试：

```bash
./vendor/bin/phpunit packages/jingdong-cloud-trade-bundle/tests
```

要运行特定的测试类：

```bash
./vendor/bin/phpunit packages/jingdong-cloud-trade-bundle/tests/Entity/AccountTest.php
```

## 测试命名规范

- 测试类：`{被测类名}Test`
- 测试方法：`test{功能描述}[_{场景描述}]`，如 `testGetAccessToken_refreshToken`

## 模拟和依赖注入

- 使用 PHPUnit 的 `createMock()` 方法创建模拟对象
- 使用依赖注入替代静态方法和全局状态
- 使用 `setAccessible(true)` 访问受保护或私有的方法和属性进行测试

## 断言粒度

测试用例尽可能覆盖以下场景：

- 正常流程和预期行为
- 异常情况和边界条件
- 空值和无效输入处理
- 不同输入组合的行为
