# log-tool
"우럭아왜우럭" 게임서버 구축 프로젝트의 로그 DB 저장 도구

---
### LOG DB 정리
    * docs > log 테이블 정의.xlsx
        * log 테이블 정의 파일에서 DW/운영 데이블 정의 참고
    * crontab
        *scribe log DB 생성
        *05 00 * * * /usr/bin/php -e /game/public_html/LogTool/Main/Cron/CreateLogDB.php
        *scribe log Insert 명령어
        *15 00 * * * /usr/bin/php -e /game/public_html/LogTool/Main/Cron/InsertLogDB.php

---
### 현재 진행상황 (2022-03-25)
* crontab 스케줄러 테스트 진행 완료
* DB 생성, Insert 쿼리 빠뜨린 부분 추가
* 로그 파일에 날짜 제거했지만, 테이블명에는 날짜 추가 진행하여 Create, Insert 로직 수정 
* Insert한 log 처리 고민
