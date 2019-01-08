BalloonEditor
  .create(document.querySelector('#content-editor'))
  .then(editor => {
    document.body.classList.remove('loading');

    /*
        const toolbarContainer = document.querySelector('#toolbar-container');
        toolbarContainer.appendChild(editor.ui.view.toolbar.element);
    */
    resizeIframe();
    editor.model.document.on('change:data', () => {
      setTimeout(resizeIframe, 0);
    });

  })
  .catch(error => {console.error(error);});

function resizeIframe() {
  window.frameElement.style.height = window.document.body.scrollHeight + 'px';
}
