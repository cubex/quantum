import InlineIFrameEditor from "@packaged-ui/ckeditor5-editor-iframe/src/inline";
import {getIframeDocument} from '@packaged-ui/ckeditor5-editor-iframe/src/shared';
import Alignment from '@ckeditor/ckeditor5-alignment/src/alignment';
import Essentials from '@ckeditor/ckeditor5-essentials/src/essentials';
import Autoformat from '@ckeditor/ckeditor5-autoformat/src/autoformat';
import Bold from '@ckeditor/ckeditor5-basic-styles/src/bold';
import Italic from '@ckeditor/ckeditor5-basic-styles/src/italic';
import Underline from "@ckeditor/ckeditor5-basic-styles/src/underline";
import BlockQuote from '@ckeditor/ckeditor5-block-quote/src/blockquote';
import Heading from '@ckeditor/ckeditor5-heading/src/heading';
import Image from '@ckeditor/ckeditor5-image/src/image';
import ImageCaption from '@ckeditor/ckeditor5-image/src/imagecaption';
import ImageStyle from '@ckeditor/ckeditor5-image/src/imagestyle';
import ImageToolbar from '@ckeditor/ckeditor5-image/src/imagetoolbar';
import Link from '@ckeditor/ckeditor5-link/src/link';
import List from '@ckeditor/ckeditor5-list/src/list';
import MediaEmbed from '@ckeditor/ckeditor5-media-embed/src/mediaembed';
import Paragraph from '@ckeditor/ckeditor5-paragraph/src/paragraph';
import PasteFromOffice from '@ckeditor/ckeditor5-paste-from-office/src/pastefromoffice';
import Table from '@ckeditor/ckeditor5-table/src/table';
import TableToolbar from '@ckeditor/ckeditor5-table/src/tabletoolbar';
import Filer from '@packaged-ui/ckeditor5-filer';
import Layout from '@packaged-ui/ckeditor5-layout';

let editor = {};
editor.config = {
  placeholder: 'Please type your content here...',
  plugins: [
    Alignment,
    Essentials,
    Autoformat,
    Bold,
    Italic,
    Underline,
    BlockQuote,
    Heading,
    Image,
    ImageCaption,
    ImageStyle,
    ImageToolbar,
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
      'underline',
      'link',
      'bulletedList',
      'numberedList',
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
};

editor.Init = (selector, config, resources) =>
{
  InlineIFrameEditor.create(
    document.querySelector(selector),
    config || window.Quantum.Editor.config
  ).then(
    function (editor)
    {
      if(resources)
      {
        resources.forEach(
          function (url)
          {
            // clone it and add into iframe head
            const link = document.createElement('link');
            link.setAttribute('href', url);
            link.setAttribute('rel', 'stylesheet');
            link.setAttribute('type', 'text/css');
            //get iframe
            const iframeHead = getIframeDocument(editor.iframeElement).head;
            iframeHead.appendChild(link);
          }
        );
      }
    }
  );
};

window.Quantum = window.Quantum || {};
window.Quantum.Editor = editor;
