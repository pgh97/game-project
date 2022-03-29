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

