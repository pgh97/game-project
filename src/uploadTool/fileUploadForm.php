<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>기획데이터 업로드</title>
    <script type="text/javascript">
        function formSubmit(f) {
            // 업로드 할 수 있는 파일 확장자를 제한합니다.
            var extArray = new Array('xls', 'xlsx');
            var path = document.getElementById("fileToUpload").value;
            if (path == "") {
                alert("파일을 선택해 주세요.");
                return false;
            }

            var pos = path.indexOf(".");
            if (pos < 0) {
                alert("확장자가 없는파일 입니다.");
                return false;
            }

            var ext = path.slice(path.indexOf(".") + 1).toLowerCase();
            var checkExt = false;
            for (var i = 0; i < extArray.length; i++) {
                if (ext == extArray[i]) {
                    checkExt = true;
                    break;
                }
            }

            if (checkExt == false) {
                alert("업로드 할 수 없는 파일 확장자 입니다.");
                return false;
            }
            return true;
        }

    </script>
</head>
<body>
<h1>기획데이터 파일                                    업로드 페이지</h1>
<h5>.xls, .xlsx의 단순 엑셀 파일만 업로드 가능</h5>
<br>
<form name="uploadForm" id="uploadForm" method="POST" action="upload.php"
      enctype="multipart/form-data" onsubmit="return formSubmit(this);">
    <input type="file" name="fileToUpload" id="fileToUpload"/>
    <input type="submit" value="upload" name="submit"/>
</form>
</body>
</html>