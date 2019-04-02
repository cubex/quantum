window.QuantumEditorConfig = window.QuantumEditorConfig || {};

QuantumWidgetEditor
  .create(
    document.querySelector('.content-editor'),
    Object.assign(
      { // enforced
        iframe: true,
      },
      QuantumEditorConfig,
      { // defaults
        filer: {
          // Upload the images to the server using the CKFinder QuickUpload command.
          uploadUrl: '/uploads/filer',

          // Define the CKFinder configuration (if necessary).
          options: {
            meta: ['image', 'video']
          }
        }
      }
    )
  )
  .then(
    editor =>
    {

      //    import {toWidget} from '@ckeditor/ckeditor5-widget/src/utils';
      //    import {downcastElementToElement} from '@ckeditor/ckeditor5-engine/src/conversion/downcast-converters';
      //    import {upcastElementToElement} from '@ckeditor/ckeditor5-engine/src/conversion/upcast-converters';

    }
  )
  .catch(error => {console.error(error);});
