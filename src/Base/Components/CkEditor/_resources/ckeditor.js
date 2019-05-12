import InlineIframeEditor from "@packaged-ui/ckeditor5-editor-iframe/src/inline";
import Alignment from '@ckeditor/ckeditor5-alignment/src/alignment';
import Essentials from '@ckeditor/ckeditor5-essentials/src/essentials';
import Autoformat from '@ckeditor/ckeditor5-autoformat/src/autoformat';
import Bold from '@ckeditor/ckeditor5-basic-styles/src/bold';
import Italic from '@ckeditor/ckeditor5-basic-styles/src/italic';
import BlockQuote from '@ckeditor/ckeditor5-block-quote/src/blockquote';
import Heading from '@ckeditor/ckeditor5-heading/src/heading';
import Image from '@ckeditor/ckeditor5-image/src/image';
import ImageCaption from '@ckeditor/ckeditor5-image/src/imagecaption';
import ImageStyle from '@ckeditor/ckeditor5-image/src/imagestyle';
import ImageToolbar from '@ckeditor/ckeditor5-image/src/imagetoolbar';
import ImageUpload from '@ckeditor/ckeditor5-image/src/imageupload';
import Link from '@ckeditor/ckeditor5-link/src/link';
import List from '@ckeditor/ckeditor5-list/src/list';
import MediaEmbed from '@ckeditor/ckeditor5-media-embed/src/mediaembed';
import Paragraph from '@ckeditor/ckeditor5-paragraph/src/paragraph';
import PasteFromOffice from '@ckeditor/ckeditor5-paste-from-office/src/pastefromoffice';
import Table from '@ckeditor/ckeditor5-table/src/table';
import TableToolbar from '@ckeditor/ckeditor5-table/src/tabletoolbar';
import Filer from '@packaged-ui/ckeditor5-filer/src/filer';
import Layout from '@packaged-ui/ckeditor5-layout/src/layout';

window.QuantumEditorConfig = window.QuantumEditorConfig || {};

// delay creating editor
document.addEventListener(
  'DOMContentLoaded',
  function ()
  {
    InlineIframeEditor.create(
      document.querySelector('.content-editor'),
      Object.assign(
        QuantumEditorConfig,
        { // defaults
          placeholder: 'Please type your content here...',
          plugins: [
            Alignment,
            Essentials,
            Autoformat,
            Bold,
            Italic,
            BlockQuote,
            Heading,
            Image,
            ImageCaption,
            ImageStyle,
            ImageToolbar,
            ImageUpload,
            Link,
            List,
            MediaEmbed,
            Paragraph,
            PasteFromOffice,
            Table,
            TableToolbar,
            Filer,
            Layout
          ],
          filer: {
            url: '/admin/quantum/upload/connector',
            options: {
              meta: ['image', 'video']
            }
          },
          toolbar: {
            items: [
              'heading',
              '|',
              'alignment',
              'bold',
              'italic',
              'link',
              'bulletedList',
              'numberedList',
              'imageUpload',
              'blockQuote',
              'insertTable',
              'mediaEmbed',
              'undo',
              'redo',
              '|', 'filer',
              '|', 'layout'
            ]
          },
          image: {
            toolbar: [
              'imageStyle:full',
              'imageStyle:side',
              '|',
              'imageTextAlternative'
            ]
          },
          table: {
            contentToolbar: [
              'tableColumn',
              'tableRow',
              'mergeTableCells'
            ]
          },
          // This value must be kept in sync with the language defined in webpack.config.js.
          language: 'en'
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
  }
);
