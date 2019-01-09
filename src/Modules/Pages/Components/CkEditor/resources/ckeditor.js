let valueElement = window.top.document.querySelector('#' + window.frameElement.getAttribute('gi'));
BalloonEditor
  .create(document.querySelector('#content-editor'))
  .then(editor => {
    document.body.classList.remove('loading');

    let popupElement = document.querySelector('.ck-body');
    editor.setData(valueElement.value);

    // copy styles
    window.top.document.head.appendChild(window.document.querySelector('style').cloneNode(true));
    // move popup container to parent
    window.frameElement.parentNode.appendChild(popupElement);

    // when popup is shown, reposition it based on frameElement
    editor.plugins.get('BalloonToolbar').on('show', () => {
      let panel = popupElement.querySelector('.ck-balloon-panel');
      panel.style.marginTop = window.frameElement.getBoundingClientRect().top + 'px';
      panel.style.marginLeft = window.frameElement.getBoundingClientRect().left + 'px';
    });

    // resize Iframe when content changes
    resizeIframe();
    editor.model.document.on('change:data', () => {
      setTimeout(resizeIframe, 0);
      // update value
      valueElement.value = editor.getData();
    });

  })
  .catch(error => {console.error(error);});

function resizeIframe() {
  window.frameElement.style.height = window.document.body.scrollHeight + 'px';
}
