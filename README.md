# game-project
"우럭아왜우럭" 게임서버 구축 프로젝트

---
### 기획데이터 정리
    * 기획데이터 표현 방법 : EXECL
    * 기획데이터 저장 방법 : DB, Memory Cache (MySQL, Redis 사용)
    * docs > DataDocs > 기획과 인게임 데이터 테이블 정의.xlsx
        * 기획 데이터 정의, 인게임 데이터 정의 시트 참고
    * docs > DataDocs > 기획 데이터.xlsx
        * 임시 기획 데이터
    * docs > DataDocs > fishgame.sql
        * 기획, 인게임 관련 테이블 생성 쿼리
    * docs > DataDocs > fishgame_data.sql
        * 기획 테이블 생성 및 임시 데이터 쿼리
    * docs > DataDocs > fishgame_erd.mwb
        * DB 테이블 erd
    * docs > DataDocs > data
        * 기획 데이터 테이블별 excel 파일들
---
### 기획 데이터 저장툴 개발 완료
* 기획 데이터를 저장소에 저장하는 툴 개발 (http://localhost:8888/src/UploadTool/excelFileUploadForm.php)
  * composer, slim, phpexcel 설치
  * 파일 업로드 기능 추가
  * 엑셀 파일을 읽어 자동으로 Drop Table 쿼리, Create Table 쿼리, Data Insert 쿼리 실행
  * ![기획데이터 업로드 페이지](https://user-images.githubusercontent.com/97434281/153375531-bf153072-1ec3-4e12-a891-ff5891aff55c.PNG)

---
### API 기능 정리
    * docs > APIDocs > API명세서.xlsx
        * API 명세서, 오류 코드 등
---
### 프로젝트 구조
    * LogTool
        * 로그서버 파일 DB 저장 툴이 있는 경로
    * public/index.php 
        * http 요청을 받는 default 경로
    * app/dependencies.php
        * PDO와 Redis 의존성 연결
    * app/settings.php
        * .env 파일에서 필요한 설정 세팅
    * app/routes.php
        * Action.php 파일과 매핑되는 API 명칭 경로
    * app/repositories.php
        * Repositories끼리 매핑시켜주는 경로  
    * src/Application
        * routing에 연결될 Action 파일들이 있는 경로
    * src/Domain
        * Service, Repository 파일이 있는 경로
    * src/Infrastructure/Persistence
        * Repository 쿼리가 있는 경로
    * src/UploadTool
        * 기획데이터 저장툴이 있는 경로 (http://localhost:8888)
---
### 로그서버 개발 정리
    * LogTool 폴더 참고
      * 별도 VM 생성하여 로그 서버 구성
      * 게임 서버에서 넘긴 scribe log를 로컬 파일로 저장 
      * LogTool은 일별로 로그 DB 생성하여 데이터 저장하는 방식
---
### 교육 과정 진행 일정
* 개발환경 세팅 (2022.01.10 ~ 2022.01.14)
* DB 교육 (2022.01.17 ~ 2022.01.21)
* 기획서 분석 및 기획 데이터 설계 (2022.01.24 ~ 2022.01.28)
* DB 설계 및 ERD 다이어그램 (2022.02.03 ~ 2022.02.09)
* API 설계 (2022.02.10 ~ 2022.02.16)
* Slim 프레임워크 및 Redis 학습 적용 (2022.02.17 ~ 2022.02.25)
* 신입 공채 게임잼 참여 (2022.02.28 ~ 2022.03.04)
* 콘텐츠 기능 API 개발 (2022.03.07 ~ 2022.03.18)
* 로그 서버 설계 및 DB 수집 (2022.03.21 ~ 2022.03.25)
* 문서 및 발표 자료 준비 (2022.03.28 ~ 2022.03.30)