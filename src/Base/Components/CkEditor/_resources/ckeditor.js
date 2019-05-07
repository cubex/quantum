window.QuantumEditorConfig = window.QuantumEditorConfig || {};

// delay creating editor
setTimeout(
  function ()
  {
    QuantumWidgetEditor
      .create(
        document.querySelector('.content-editor'),
        Object.assign(
          { // enforced
            iframe: true,
          },
          QuantumEditorConfig,
          { // defaults
            placeholder: 'Please type your content here...',
            filer: {
              url: '/admin/quantum/upload/connector',
              options: {
                meta: ['image', 'video']
              }
            }
          }
        )
      )
      .then(
        function (editor)
        {

          //    import {toWidget} from '@ckeditor/ckeditor5-widget/src/utils';
          //    import {downcastElementToElement} from '@ckeditor/ckeditor5-engine/src/conversion/downcast-converters';
          //    import {upcastElementToElement} from '@ckeditor/ckeditor5-engine/src/conversion/upcast-converters';

        }
      )
      .catch(function (error) {console.error(error);});
  },
  0
);
