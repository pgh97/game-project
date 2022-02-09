# game-project
"우럭아왜우럭" 게임서버 구축 프로젝트

---
### 기획데이터 정리
    * 기획데이터 표현 방법 : EXECL
    * 기획데이터 저장 방법 : DB, Memory Cache (MySQL, Redis 사용)
    * DataDocs > 기획과 인게임 데이터 테이블 정의.xlsx
        * 기획 데이터 정의, 인게임 데이터 정의 시트 참고
    * DataDocs > 기획 데이터.xlsx
        * 임시 기획 데이터
    * DataDocs > fishgame.sql
        * 기획, 인게임 관련 테이블 생성 쿼리
    * DataDocs > fishgame_data.sql
        * 기획 테이블 생성 및 임시 데이터 쿼리
    * DataDocs > fishgame_erd.mwb
        * DB 테이블 erd
---
### API 기능 정리
    * APIDocs > API명세서.xlsx
        * API 명세서 작성 중.
---
### 현재 진행상황
* DB(MySQL) 테이블 속성 일부 수정
* API 기능 정리 중
* 기획 데이터를 저장소에 저장하는 툴 개발
  * composer, slim, phpexcel 설치
  * 파일 업로드 기능과 DB 쿼리 생성하여 데이터 넣는 기능 추가