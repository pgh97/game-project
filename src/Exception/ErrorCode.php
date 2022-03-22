<?php

namespace App\Exception;

class ErrorCode
{
    public const SUCCESS = 'SUCCESS';
    public const SUCCESS_CREATED = 'SUCCESS_CREATED';
    public const GIFT_SUCCESS = 'GIFT_SUCCESS';
    public const LEVEL_SUCCESS = 'LEVEL_SUCCESS';
    public const SELL_SUCCESS = 'SELL_SUCCESS';
    public const FULL_SUCCESS = 'FULL_SUCCESS';
    public const BUY_SUCCESS = 'BUY_SUCCESS';
    public const UPGRADE_SUCCESS = 'UPGRADE_SUCCESS';

    public const FAIL_FUNCTION = 'FAIL_FUNCTION';
    public const NOT_CONTENTS = 'NOT_CONTENTS';

    public const BAD_REQUEST = 'BAD_REQUEST';
    public const INSUFFICIENT_PRIVILEGES = 'INSUFFICIENT_PRIVILEGES';
    public const NOT_ALLOWED = 'NOT_ALLOWED';
    public const NOT_IMPLEMENTED = 'NOT_IMPLEMENTED';
    public const RESOURCE_NOT_FOUND = 'RESOURCE_NOT_FOUND';
    public const SERVER_ERROR = 'SERVER_ERROR';
    public const UNAUTHENTICATED = 'UNAUTHENTICATED';
    public const VALIDATION_ERROR = 'VALIDATION_ERROR';
    public const VERIFICATION_ERROR = 'VERIFICATION_ERROR';

    public const REDUPLICATION_CONTENTS = 'REDUPLICATION_CONTENTS';
    public const FULL_DATA = 'FULL_DATA';
    public const NOT_FULL_SHIP = 'NOT_FULL_SHIP';
    public const NOT_LEVEL = 'NOT_LEVEL';
    public const MISS_FISH = 'MISS_FISH';
    public const NOT_FULL_FISHING_ITEM = 'NOT_FULL_FISHING_ITEM';
    public const SELL_FAIL = 'SELL_FAIL';
    public const NO_MONEY = 'NO_MONEY';
    public const ALREADY_FULL = 'ALREADY_FULL';
    public const NO_PARAM = 'NO_PARAM';
    public const UPGRADE_FAIL = 'UPGRADE_FAIL';
    public const NO_UPGRADE_ITEM = 'NO_UPGRADE_ITEM';

    private array $errorArray;

    /**
     * @return array
     */
    public function getErrorArray(): array
    {
        return $this->errorArray;
    }

    /**
     * @param array $errorArray
     */
    public function setErrorArray(array $errorArray): void
    {
        $this->errorArray = $errorArray;
    }

    /**
     * @return array
     */
    public function getErrorArrayItem(string $codeStr): array
    {
        return $this->errorArray[$codeStr];
    }

    public function __construct()
    {
        $this->errorArray=array(
            'SUCCESS' => array(
                'statusCode' => 200,
                'message' => '성공했습니다.'
            ),
            'SUCCESS_CREATED' => array(
                'statusCode' => 2001,
                'message' => '생성/수정을 성공했습니다.'
            ),
            'GIFT_SUCCESS' => array(
                'statusCode' => 2002,
                'message' => '선물을 받았습니다.'
            ),
            'LEVEL_SUCCESS' => array(
                'statusCode' => 2003,
                'message' => '레벨업 했습니다.'
            ),
            'SELL_SUCCESS' => array(
                'statusCode' => 2004,
                'message' => '판매에 성공했습니다.'
            ),
            'FULL_SUCCESS' => array(
                'statusCode' => 2005,
                'message' => '수리에 성공했습니다.'
            ),
            'BUY_SUCCESS' => array(
                'statusCode' => 2006,
                'message' => '구매에 성공했습니다.'
            ),
            'UPGRADE_SUCCESS' => array(
                'statusCode' => 2007,
                'message' => '업그레이드에 성공했습니다.'
            ),
            'BAD_REQUEST' => array(
                'statusCode' => 4000,
                'message' => '잘못된 요청입니다.'
            ),
            'UNAUTHENTICATED' => array(
                'statusCode' => 4001,
                'message' => '인증체크 실패입니다.'
            ),
            'NO_PARAM' => array(
                'statusCode' => 4002,
                'message' => '값을 입력해주세요.'
            ),
            'REDUPLICATION_CONTENTS' => array(
                'statusCode' => 5001,
                'message' => '이미 데이터가 있습니다.'
            ),
            'FULL_DATA' => array(
                'statusCode' => 5002,
                'message' => '데이터가 꽉찼습니다. 확인해주세요.'
            ),
            'NOT_FULL_SHIP' => array(
                'statusCode' => 5003,
                'message' => '보로롱24의 내구도와 연로 혹은 피로도를 확인해주세요.'
            ),
            'NOT_LEVEL' => array(
                'statusCode' => 5004,
                'message' => '최소레벨을 충족하지 못해서 입장할 수 없습니다.'
            ),
            'MISS_FISH' => array(
                'statusCode' => 5005,
                'message' => '물고기를 놓쳤습니다.'
            ),
            'NOT_FULL_FISHING_ITEM' => array(
                'statusCode' => 5006,
                'message' => '채비의 갯수와 내구를 확인해주세요.'
            ),
            'SELL_FAIL' => array(
                'statusCode' => 5007,
                'message' => '판매할 수 있는 갯수가 부족합니다.'
            ),
            'NO_MONEY' => array(
                'statusCode' => 5008,
                'message' => '재화가 부족합니다.'
            ),
            'UPGRADE_FAIL' => array(
                'statusCode' => 5009,
                'message' => '업그레이드에 실패했습니다...'
            ),
            'NO_UPGRADE_ITEM' => array(
                'statusCode' => 5010,
                'message' => '업그레이드에 필요한 부품이 부족합니다.'
            ),
            'FAIL_FUNCTION' => array(
                'statusCode' => 8000,
                'message' => 'API 실패했습니다. 데이터를 확인해주세요.'
            ),
            'NOT_CONTENTS' => array(
                'statusCode' => 8001,
                'message' => '데이터가 없습니다. 등록해주세요.'
            ),
            'ALREADY_FULL' => array(
                'statusCode' => 8002,
                'message' => '이미 최대치입니다.'
            )
        );
    }
}