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
* 기획 데이터를 저장소에 저장하는 툴 개발 (http://localhost/src/uploadTool/excelFileUploadForm.php)
  * composer, slim, phpexcel 설치
  * 파일 업로드 기능 추가
  * 엑셀 파일을 읽어 자동으로 Drop Table 쿼리, Create Table 쿼리, Data Insert 쿼리 실행
  * ![기획데이터 업로드 페이지](https://user-images.githubusercontent.com/97434281/153375531-bf153072-1ec3-4e12-a891-ff5891aff55c.PNG)

---
### API 기능 정리
    * docs > APIDocs > API명세서.xlsx
        * API 명세서 작성 중
---
### 현재 진행상황 (2022-02-18)
* API 기능 명세서 작성 중
* Slim Framework 학습 진행