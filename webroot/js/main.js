{
    "use strict";

    function returnFileUrl(fileUrl, filePath, isEditor) {
        if (isEditor) {
            parent.postMessage({mceAction: 'insert', content: fileUrl});
            parent.postMessage({mceAction: 'close'});
        } else {
            window.opener.onWindowClose(fileUrl, filePath);
            window.close();
        }
    }

}
