window.returnFileUrl = function (fileUrl, filePath, isEditor) {
    if (isEditor) {
        parent.postMessage({ mceAction: 'insert', content: fileUrl });
        parent.postMessage({ mceAction: 'close' });
    } else {
        window.parent.postMessage({
            type: 'file-selected',
            url: fileUrl,
            path: filePath
        });
    }
};
