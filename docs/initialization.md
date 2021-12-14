## 应用初始化

参数

| 字段      | 类型   | 必填 | 说明                                                                                                |
| --------- | ------ | ---- | --------------------------------------------------------------------------------------------------- |
| secretId  | string | 否   | 腾讯云 API 固定密钥对，在云函数内执行可不填。[前往获取](https://console.cloud.tencent.com/cam/capi) |
| secretKey | string | 否   | 同上                                                                                                |
| sessionToken | string | 否   | 临时密钥 sesstionToken，使用临时密钥时该字段必填。                                                  |
| env       | string | 否   | TCB 环境 ID，不填使用默认环境                                                                       |
| proxy     | string | 否   | 调用接口时使用的 http 代理 url                                                                      |
| timeout   | double | 否   | 调用接口的超时时间（s），默认为 15，即 15 秒                                                        |

```javascript
// 初始化示例
use TencentCloudBase\TCB;

// 初始化资源
// 云函数下不需要secretId和secretKey。
// env如果不指定将使用默认环境
$tcb = new Tcb([
  'secretId'=> "xxxxx",
  'secretKey'=> "xxxx",
  'env'=> "xxx"
]);

//云函数下使用默认环境
$tcb = new Tcb([]);

//云函数下指定环境
$tcb = new Tcb(['env'=> "xxx"]);

//修改请求超时时间
$tcb = new Tcb(['timeout'=> 5]);

```
