const inputId = window.frameElement.getAttribute('gi');
const input = window.parent.document.getElementById(inputId);

const editor = grapesjs
  .init(
    {
      container: '#content-editor',
      fromElement: false,
      height: '100%',
      width: '100%',
      forceClass: false,
      plugins: ['gjs-preset-newsletter'],
      storageManager: {
        type: 'quantum-remote',
        autoload: true,
        autosave: true,
      },
      blockManager: {
        blocks: [
          {
            id: 'section', // id is mandatory
            label: 'Section', // You can use HTML/SVG inside labels
            attributes: {class: 'gjs-block-section'},
            content: `<section>
          <h1>This is a simple title</h1>
          <div>This is just a Lorem text: Lorem ipsum dolor sit amet</div>
        </section>`,
          },
          {
            id: 'div',
            label: 'Div',
            content: '<div>Insert your content here</div>',
          },
          {
            id: 'image',
            label: 'Image',
            // Select the component once it's dropped
            select: true,
            // You can pass components as a JSON instead of a simple HTML string,
            // in this case we also use a defined component type `image`
            content: {type: 'image'},
            // This triggers `active` event on dropped components and the `image`
            // reacts by opening the AssetManager
            activate: true,
          }
        ]
      }
    }
  );

editor.on(
  'load',
  function () {
    document.body.classList.remove('loading');
  }
);

editor.StorageManager.add(
  'quantum-remote',
  {
    // New logic for the local storage
    load: function (keys, clb, clbErr) {
      console.log('load');
      clb({'gjs-html': input.value});
      /*
      let xhr = new XMLHttpRequest();
      xhr.open('POST', window.frameElement.getAttribute('src') + '/save');
      xhr.addEventListener(
        'readystatechange',
        function () {
          if(xhr.readyState === XMLHttpRequest.DONE)
          {
            console.log('load', xhr);
            clb({'gjs-html': xhr.textContent});
          }
        }
      );
      xhr.send();*/
    },

    store: function (data, clb, clbErr) {
      input.value = editor.runCommand('gjs-get-inlined-html');
      /*
      let xhr = new XMLHttpRequest();
      xhr.open('POST', window.frameElement.getAttribute('src') + '/save');
      let frmData = new FormData();
      frmData.append('data', editor.runCommand('gjs-get-inlined-html'));
      xhr.send(frmData);
      clb();*/
    }
  }
);
