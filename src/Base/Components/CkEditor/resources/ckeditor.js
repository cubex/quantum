WidgetEditor
  .create(document.querySelector('textarea.content-editor'))
  .then(editor => {

//    import {toWidget} from '@ckeditor/ckeditor5-widget/src/utils';
//    import {downcastElementToElement} from '@ckeditor/ckeditor5-engine/src/conversion/downcast-converters';
//    import {upcastElementToElement} from '@ckeditor/ckeditor5-engine/src/conversion/upcast-converters';

  })
  .catch(error => {console.error(error);});
