<?php
namespace TencentCloudBase\Consts;

class Code
{
  const SUCCESS = 'SUCCESS'; //0;
  const ERROR = 'ERROR'; //100000;

  // 100 表示 通用错误 
  // 400 表示 sdk 服务端错误
  // 500 表示 sdk 客户端错误

  const FUNCTIONS_NAME_REQUIRED = 'FUNCTIONS_NAME_REQUIRED'; //400000;

  const INVALID_PARAM = 'INVALID_PARAM'; //400001

  const EMPTY_PARAM = 'EMPTY_PARAM';

  const INVALID_TYPE = 'INVALID_TYPE';

  const INVALID_RANGE = 'INVALID_RANGE';

  const DocIDError = 'DocIDError';

  const CollNameError = 'CollNameError';

  const OpStrError = 'OpStrError';

  const DirectionError = 'DirectionError';

  const IntergerError = 'IntergerError';

  const INVALID_FIELD_PATH = 'INVALID_FIELD_PATH';

  const DATABASE_REQUEST_FAILED = 'DATABASE_REQUEST_FAILED';

  // 随机数生成位数
  const EVENTID_NUM = 5;
}
